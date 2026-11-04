<?php

namespace Tests\Feature;

use App\Models\{Client, Invoice, Project, ProjectNote, SupportPlan, Task, TimeEntry, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function workspace(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);
        $client = Client::factory()->create(['created_by' => $admin->id]);
        $project = Project::factory()->create(['client_id' => $client->id, 'created_by' => $admin->id]);
        $project->users()->attach($member, ['role' => 'member']);
        $task = Task::factory()->create(['project_id' => $project->id, 'created_by' => $admin->id]);
        $task->assignees()->attach($member, ['assigned_by' => $admin->id]);
        return [$admin, $member, $client, $project, $task];
    }

    private function invoiceData(Client $client): array
    {
        return ['client_id'=>$client->id, 'status'=>'draft', 'currency'=>'CZK', 'tax_rate'=>21,
            'items'=>[['name'=>'Vývoj webu','qty'=>2,'unit_price'=>1000]]];
    }

    public function test_quick_status_change_preserves_task_fields_and_requires_project_access(): void
    {
        [$admin, $member, , , $task] = $this->workspace();
        $original = $task->only(['title', 'description', 'priority', 'project_id']);
        $this->actingAs($member)->from('/tasks?view=mine&sort=due')
            ->patch(route('tasks.status', $task), ['status'=>'review', 'title'=>'Ignored'])
            ->assertRedirect('/tasks?view=mine&sort=due');
        $this->assertSame('review', $task->fresh()->status);
        $this->assertSame($original, $task->fresh()->only(array_keys($original)));
        $this->patch(route('tasks.status', $task), ['status'=>'unknown'])->assertSessionHasErrors('status');
        $stranger = User::factory()->create(['role'=>'member']);
        $this->actingAs($stranger)->patch(route('tasks.status', $task), ['status'=>'done'])->assertForbidden();
        $this->assertSame('review', $task->fresh()->status);
    }

    public function test_task_views_keep_completed_and_unassigned_work_out_of_my_open_tasks(): void
    {
        [$admin, $member, , $project, $task] = $this->workspace();
        $task->update(['title'=>'My open work', 'status'=>'todo']);
        $done = Task::factory()->create(['project_id'=>$project->id,'title'=>'My finished work','status'=>'done']);
        $done->assignees()->attach($member, ['assigned_by'=>$admin->id]);
        Task::factory()->create(['project_id'=>$project->id,'title'=>'Other open work','status'=>'todo']);
        $this->actingAs($member)->get('/tasks?view=mine')->assertOk()
            ->assertSee('My open work')->assertDontSee('My finished work')->assertDontSee('Other open work');
        $this->get('/tasks?view=open')->assertSee('Other open work')->assertDontSee('My finished work');
        $this->actingAs($admin)->get('/user-management?q='.$member->email)->assertOk()->assertSee($member->email);
    }

    public function test_invoice_belongs_to_client_without_requiring_a_project(): void
    {
        [$admin,,$client] = $this->workspace();
        $this->actingAs($admin)->post(route('invoices.store'), $this->invoiceData($client))->assertRedirect();
        $invoice = Invoice::firstOrFail();
        $this->assertSame($client->id, $invoice->client_id);
        $this->assertNull($invoice->project_id);
        $this->assertEquals(2420, $invoice->total);
        $this->assertCount(1, $invoice->items);
    }

    public function test_invoice_rejects_another_clients_project_on_create_and_update(): void
    {
        [$admin,,$client,$project] = $this->workspace();
        $otherClient = Client::factory()->create(['created_by'=>$admin->id]);
        $data = $this->invoiceData($otherClient) + ['project_id'=>$project->id];
        $this->actingAs($admin)->post(route('invoices.store'), $data)->assertSessionHasErrors('project_id');
        $this->assertDatabaseCount('invoices', 0);
        $this->post(route('invoices.store'), $this->invoiceData($client))->assertRedirect();
        $invoice = Invoice::firstOrFail();
        $this->put(route('invoices.update', $invoice), $data)->assertSessionHasErrors('project_id');
        $this->assertSame($client->id, $invoice->fresh()->client_id);
    }

    public function test_project_notes_are_shared_only_with_project_members_and_escaped(): void
    {
        [$admin,$member,,$project] = $this->workspace();
        $body = '<script>alert(1)</script> Domluva na středu.';
        $this->actingAs($member)->post(route('projects.notes.store', $project), ['body'=>$body])->assertRedirect();
        $this->get(route('projects.show', [$project,'tab'=>'notes']))->assertOk()
            ->assertSee($body)->assertDontSee($body, false);
        $note = ProjectNote::firstOrFail();
        $stranger = User::factory()->create(['role'=>'member']);
        $this->actingAs($stranger)->post(route('projects.notes.store',$project), ['body'=>'Hidden'])->assertForbidden();
        $this->delete(route('projects.notes.destroy',[$project,$note]))->assertForbidden();
        $this->actingAs($admin)->get(route('projects.show',[$project,'tab'=>'notes']))->assertSee($body);
    }

    public function test_note_author_can_delete_but_a_colleague_cannot_delete_it(): void
    {
        [, $member,,$project] = $this->workspace();
        $note = $project->notes()->create(['user_id'=>$member->id,'body'=>'Domluva']);
        $colleague = User::factory()->create(['role'=>'member']);
        $project->users()->attach($colleague);
        $this->actingAs($colleague)->delete(route('projects.notes.destroy',[$project,$note]))->assertForbidden();
        $this->actingAs($member)->delete(route('projects.notes.destroy',[$project,$note]))->assertRedirect();
        $this->assertModelMissing($note);
    }

    public function test_timer_survives_navigation_and_allows_only_one_running_entry_per_person(): void
    {
        [, $member,,$project,$task] = $this->workspace();
        $otherTask = Task::factory()->create(['project_id'=>$project->id]);
        $this->freezeTime();
        $this->actingAs($member)->from(route('tasks.show',$task))->post(route('tasks.timer.start',$task))->assertRedirect();
        $entry = TimeEntry::firstOrFail();
        $this->assertNull($entry->ended_at);
        $this->get(route('dashboard'))->assertOk()->assertSee('Běžící měření času')->assertSee($task->title);
        $this->get(route('clients.show',$project->client))->assertOk()->assertSee('Zastavit a uložit');
        $this->post(route('tasks.timer.start',$otherTask))->assertSessionHasErrors('timer');
        $this->assertDatabaseCount('time_entries',1);
        $this->travel(65)->minutes();
        $this->post(route('timers.stop',$entry),['notes'=>'Oprava formuláře'])->assertRedirect();
        $this->assertSame(65,$entry->fresh()->duration_in_minutes);
        $end = $entry->fresh()->ended_at->toIso8601String();
        $this->travel(5)->minutes();
        $this->post(route('timers.stop',$entry))->assertRedirect();
        $this->assertSame($end,$entry->fresh()->ended_at->toIso8601String());
    }

    public function test_two_people_can_track_independently_and_cannot_stop_each_other(): void
    {
        [$admin,$member,,,$task] = $this->workspace();
        $this->actingAs($member)->post(route('tasks.timer.start',$task))->assertRedirect();
        $entry = TimeEntry::firstOrFail();
        $this->actingAs($admin)->post(route('tasks.timer.start',$task))->assertRedirect();
        $this->post(route('timers.stop',$entry))->assertForbidden();
        $this->assertSame(2,TimeEntry::whereNull('ended_at')->count());
    }

    public function test_inaccessible_tasks_cannot_be_timed(): void
    {
        [,,,,$task] = $this->workspace();
        $stranger = User::factory()->create(['role'=>'member']);
        $this->actingAs($stranger)->post(route('tasks.timer.start',$task))->assertForbidden();
        $this->assertDatabaseCount('time_entries',0);
    }

    public function test_manual_time_uses_prague_timezone_and_nested_routes_are_scoped(): void
    {
        [, $member,,$project,$task] = $this->workspace();
        $data = ['started_at'=>'2026-07-01T10:00','ended_at'=>'2026-07-01T11:30','notes'=>'Vývoj'];
        $this->actingAs($member)->post(route('tasks.time-entries.store',$task),$data)->assertRedirect();
        $entry = TimeEntry::firstOrFail();
        $this->assertSame('2026-07-01 08:00:00',$entry->started_at->format('Y-m-d H:i:s'));
        $this->assertSame(90,$entry->duration_in_minutes);
        $otherTask = Task::factory()->create(['project_id'=>$project->id]);
        $this->put(route('tasks.time-entries.update',[$otherTask,$entry]),$data)->assertNotFound();
        $this->delete(route('tasks.time-entries.destroy',[$otherTask,$entry]))->assertNotFound();
        $this->post(route('tasks.time-entries.store',$task), ['started_at'=>'2026-07-01T12:00'])->assertSessionHasErrors('ended_at');
    }

    public function test_service_renewal_prefills_a_new_period_without_copying_payment_or_editing_history(): void
    {
        [$admin,,$client,$project] = $this->workspace();
        $service = SupportPlan::create(['client_id'=>$client->id,'project_id'=>$project->id,'title'=>'Hosting',
            'price'=>1500,'currency'=>'CZK','period_from'=>'2026-01-01','period_to'=>'2026-12-31',
            'paid_at'=>'2026-01-02','status'=>'active','created_by'=>$admin->id]);
        $this->actingAs($admin)->get(route('support-plans.create',['renew_from'=>$service->id]))->assertOk()
            ->assertSee('2027-01-01')->assertSee('2027-12-31')->assertDontSee('value="2026-01-02"',false);
        $this->post(route('support-plans.store'),['client_id'=>$client->id,'project_id'=>$project->id,
            'title'=>'Hosting','price'=>1500,'currency'=>'CZK','period_from'=>'2027-01-01',
            'period_to'=>'2027-12-31','paid_at'=>null,'status'=>'active'])->assertRedirect();
        $this->assertDatabaseCount('support_plans',2);
        $this->assertSame('2026-12-31',$service->fresh()->period_to->toDateString());
        $this->assertSame('2026-01-02',$service->fresh()->paid_at->toDateString());
    }

    public function test_service_project_must_belong_to_the_client(): void
    {
        [$admin,,,$project] = $this->workspace();
        $other = Client::factory()->create(['created_by'=>$admin->id]);
        $this->actingAs($admin)->post(route('support-plans.store'),['client_id'=>$other->id,'project_id'=>$project->id,
            'title'=>'Správa','price'=>500,'currency'=>'CZK','period_from'=>'2026-01-01',
            'period_to'=>'2026-12-31','status'=>'active'])->assertSessionHasErrors('project_id');
    }

    public function test_workspace_pages_render_and_private_notes_do_not_leak_to_dashboard(): void
    {
        [$admin,$member,$client,$project,$task] = $this->workspace();
        $privateProject = Project::factory()->create(['client_id'=>$client->id,'created_by'=>$admin->id]);
        $privateProject->notes()->create(['user_id'=>$admin->id,'body'=>'Soukromý obsah jiného projektu']);
        $this->actingAs($member)->get(route('dashboard'))->assertOk()->assertDontSee('Soukromý obsah jiného projektu');
        $this->get(route('clients.show',$client))->assertOk()->assertDontSee($privateProject->name);
        $this->actingAs($admin);
        foreach (['/dashboard','/clients','/clients/'.$client->id,'/projects','/projects/'.$project->id,
            '/projects/'.$project->id.'?tab=notes','/projects/'.$project->id.'?tab=time',
            '/projects/'.$project->id.'?tab=overview','/tasks/'.$task->id,'/invoices/create',
            '/support-plans','/support-plans/create'] as $url) $this->get($url)->assertOk();
    }

    public function test_invoice_filter_and_summaries_keep_clients_and_currencies_separate(): void
    {
        [$admin,,$client] = $this->workspace();
        $other = Client::factory()->create(['created_by'=>$admin->id]);
        Invoice::create(['client_id'=>$client->id,'created_by'=>$admin->id,'invoice_number'=>'CLIENT-A',
            'status'=>'sent','currency'=>'CZK','total'=>1500]);
        Invoice::create(['client_id'=>$other->id,'created_by'=>$admin->id,'invoice_number'=>'CLIENT-B',
            'status'=>'sent','currency'=>'EUR','total'=>100]);
        $this->actingAs($admin)->get(route('invoices.index',['client_id'=>$client->id]))->assertOk()
            ->assertSee('CLIENT-A')->assertDontSee('CLIENT-B')
            ->assertViewHas('totalsByCurrency',fn($totals)=>$totals->keys()->all() === ['CZK'] && $totals['CZK']['outstanding'] == 1500);
        $this->get(route('invoices.index'))->assertOk()->assertViewHas('totalsByCurrency',fn($totals)=>
            $totals['CZK']['outstanding'] == 1500 && $totals['EUR']['outstanding'] == 100);
    }

    public function test_service_expiry_uses_the_prague_calendar_date_at_midnight(): void
    {
        [$admin,,$client] = $this->workspace();
        $this->travelTo(\Carbon\Carbon::parse('2026-11-04 23:30:00','UTC'));
        $service=SupportPlan::create(['client_id'=>$client->id,'created_by'=>$admin->id,'title'=>'Hosting',
            'price'=>100,'currency'=>'CZK','period_from'=>'2026-01-01','period_to'=>'2026-11-04','status'=>'active']);
        $this->assertSame(0,SupportPlan::active()->count());
        $this->assertSame(0,SupportPlan::expiringSoon()->count());
        $this->assertSame(1,SupportPlan::expired()->count());
        $this->assertFalse($service->isExpiringSoon());
    }

    public function test_initial_admin_command_hashes_the_chosen_password(): void
    {
        $this->artisan('crm:user',['email'=>'owner@example.test','--admin'=>true])
            ->expectsQuestion('Jméno','Vlastník')
            ->expectsQuestion('Heslo (alespoň 12 znaků)','Private-test-password!')
            ->expectsQuestion('Zopakujte heslo','Private-test-password!')
            ->assertSuccessful();
        $user=User::where('email','owner@example.test')->firstOrFail();
        $this->assertTrue($user->isAdmin());
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('Private-test-password!',$user->password));
    }
}
