<?php

namespace Tests\Feature;

use App\Models\{Client, Invoice, Project, SupportPlan, Task, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function invoice(User $owner, Client $client, string $number, string $currency, float $total, string $status, ?string $paidAt = null): Invoice
    {
        return Invoice::create(['client_id'=>$client->id, 'created_by'=>$owner->id,
            'invoice_number'=>$number, 'currency'=>$currency, 'total'=>$total, 'status'=>$status, 'paid_at'=>$paidAt]);
    }

    public function test_dashboard_restores_financial_overview_without_mixing_currencies_or_future_payments(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-05 12:00:00', 'Europe/Prague'));
        $owner = User::factory()->create(['role'=>'admin']);
        $client = Client::factory()->create(['created_by'=>$owner->id]);
        $this->invoice($owner,$client,'CZK-PREV','CZK',1500,'paid','2026-09-15');
        $this->invoice($owner,$client,'CZK-NOW','CZK',2000,'paid','2026-11-04');
        $this->invoice($owner,$client,'CZK-FUTURE','CZK',3000,'paid','2026-12-01');
        $this->invoice($owner,$client,'CZK-OPEN','CZK',4000,'sent');
        $this->invoice($owner,$client,'EUR-NOW','EUR',90,'paid','2026-11-04');
        $this->invoice($owner,$client,'EUR-OPEN','EUR',60,'overdue');
        foreach (['CZK'=>110,'EUR'=>70] as $currency=>$price) {
            SupportPlan::create(['client_id'=>$client->id,'created_by'=>$owner->id,'title'=>'Správa '.$currency,
                'price'=>$price,'currency'=>$currency,'period_from'=>'2026-11-01','period_to'=>'2026-11-20','status'=>'active']);
        }

        $this->actingAs($owner)->get('/dashboard?months=6&currency=CZK')->assertOk()->assertSee('Nástěnka')
            ->assertSee('Měsíční příjmy')->assertSee('Úkoly podle stavu')
            ->assertViewHas('outstandingTotal',4000.0)->assertViewHas('outstandingCount',1)
            ->assertViewHas('revenueTotal',3500.0)->assertViewHas('paidThisMonth',2000.0)
            ->assertViewHas('revenueMonths',fn($rows)=>$rows->count()===6 && $rows->first()['key']==='2026-06' && $rows->last()['total']===2000.0)
            ->assertViewHas('activeServiceCount',2)
            ->assertViewHas('activeServiceTotals',fn($totals)=>$totals['CZK']===110.0 && $totals['EUR']===70.0);
        $this->get('/dashboard?currency=EUR')->assertOk()->assertViewHas('outstandingTotal',60.0)
            ->assertViewHas('revenueTotal',90.0)->assertViewHas('paidThisMonth',90.0);
        $this->get('/invoices?currency=CZK&status=outstanding')->assertOk()->assertSee('CZK-OPEN')
            ->assertDontSee('EUR-OPEN')->assertDontSee('CZK-NOW');
    }

    public function test_dashboard_and_work_respect_project_membership_and_bound_the_month_filter(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-05 12:00:00', 'Europe/Prague'));
        $owner = User::factory()->create(['role'=>'admin']);
        $member = User::factory()->create(['role'=>'member']);
        $client = Client::factory()->create(['created_by'=>$owner->id]);
        $privateClient = Client::factory()->create(['created_by'=>$owner->id]);
        $project = Project::factory()->create(['client_id'=>$client->id,'created_by'=>$owner->id,'status'=>'active']);
        $project->users()->attach($member);
        $privateProject = Project::factory()->create(['client_id'=>$privateClient->id,'created_by'=>$owner->id,'status'=>'active']);
        $task = Task::factory()->create(['project_id'=>$project->id,'status'=>'todo','title'=>'Sdílený úkol']);
        $task->assignees()->attach($member,['assigned_by'=>$owner->id]);
        Task::factory()->create(['project_id'=>$privateProject->id,'status'=>'done','title'=>'Soukromý úkol']);
        $this->invoice($owner,$client,'VISIBLE-PAID','CZK',120,'paid','2026-11-03');
        $this->invoice($owner,$privateClient,'PRIVATE-PAID','CZK',90000,'paid','2026-11-03');
        $this->invoice($owner,$privateClient,'PRIVATE-EUR','EUR',8000,'sent');

        $this->actingAs($member)->get('/dashboard?months=999999&currency=EUR')->assertOk()
            ->assertViewHas('months',12)->assertViewHas('currency','CZK')
            ->assertViewHas('currencies',fn($rows)=>$rows->all()===['CZK'])
            ->assertViewHas('revenueTotal',120.0)->assertViewHas('activeProjects',1)
            ->assertViewHas('tasksByStatus',fn($rows)=>$rows['todo']===1 && $rows['done']===0)
            ->assertSee('VISIBLE-PAID')->assertDontSee('PRIVATE-PAID')->assertDontSee('PRIVATE-EUR');
        $this->get('/my-work')->assertOk()->assertSee('Moje práce')->assertSee('Sdílený úkol')->assertDontSee('Soukromý úkol');
    }

    public function test_empty_dashboard_has_useful_states_and_both_pages_require_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/my-work')->assertRedirect('/login');
        $owner = User::factory()->create(['role'=>'admin']);
        $this->actingAs($owner)->get('/dashboard?months=24')->assertOk()
            ->assertViewHas('months',24)->assertSee('Zatím bez uhrazených faktur')
            ->assertViewHas('outstandingTotal',0.0)->assertViewHas('revenueTotal',0.0);
        $this->get('/my-work')->assertOk();
    }
}
