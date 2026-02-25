<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Comment;
use App\Models\PasswordEntry;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\MentionNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CrmTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────────────────────────────────
    // Helper: create minimal fixture data
    // ──────────────────────────────────────────────────────────────────────────

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function makeMember(): User
    {
        return User::factory()->create(['role' => 'member']);
    }

    private function makeProject(User $creator, ?Client $client = null): Project
    {
        $client ??= Client::factory()->create(['created_by' => $creator->id]);

        return Project::factory()->create([
            'client_id'  => $client->id,
            'created_by' => $creator->id,
            'status'     => 'active',
        ]);
    }

    private function makeTask(Project $project, User $creator): Task
    {
        return Task::factory()->create([
            'project_id' => $project->id,
            'created_by' => $creator->id,
            'status'     => 'todo',
            'priority'   => 'medium',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Test 1 – Member is forbidden from viewing a project they are NOT assigned to
    // ──────────────────────────────────────────────────────────────────────────

    /** @test */
    public function member_cannot_view_project_they_are_not_assigned_to(): void
    {
        $admin  = $this->makeAdmin();
        $member = $this->makeMember();

        $project = $this->makeProject($admin);
        // Note: member is NOT attached to the project

        $response = $this->actingAs($member)
            ->get(route('projects.show', $project));

        // Policy should deny access → 403 Forbidden
        $response->assertForbidden();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Test 2 – Member CAN post a comment on a task in a project they belong to
    // ──────────────────────────────────────────────────────────────────────────

    /** @test */
    public function member_can_create_comment_on_accessible_task(): void
    {
        $admin  = $this->makeAdmin();
        $member = $this->makeMember();

        $project = $this->makeProject($admin);
        $project->users()->attach($member->id, ['role' => 'member']);

        $task = $this->makeTask($project, $admin);

        $response = $this->actingAs($member)
            ->post(route('tasks.comments.store', $task), [
                'body'      => 'This is my first comment.',
                'parent_id' => null,
            ]);

        $response->assertRedirect(route('tasks.show', $task));
        $this->assertDatabaseHas('comments', [
            'task_id' => $task->id,
            'user_id' => $member->id,
            'body'    => 'This is my first comment.',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Test 3 – @mention creates a comment_mentions record AND sends a DB notification
    // ──────────────────────────────────────────────────────────────────────────

    /** @test */
    public function mention_in_comment_creates_record_and_sends_notification(): void
    {
        Notification::fake();

        $admin  = User::factory()->create(['name' => 'AdminUser', 'role' => 'admin']);
        $member = $this->makeMember();

        $project = $this->makeProject($admin);
        $project->users()->attach($admin->id,  ['role' => 'lead']);
        $project->users()->attach($member->id, ['role' => 'member']);

        $task = $this->makeTask($project, $admin);

        // member mentions the admin by name (single-word, no spaces)
        $mentionBody = "Hey @{$admin->name}, can you review this?";

        $this->actingAs($member)
            ->post(route('tasks.comments.store', $task), [
                'body'      => $mentionBody,
                'parent_id' => null,
            ]);

        // A comment_mentions row should exist
        $comment = Comment::where('task_id', $task->id)->where('user_id', $member->id)->first();
        $this->assertNotNull($comment, 'Comment was not created');

        $this->assertDatabaseHas('comment_mentions', [
            'comment_id' => $comment->id,
            'user_id'    => $admin->id,
        ]);

        // A database notification should have been sent to the admin
        Notification::assertSentTo($admin, MentionNotification::class);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Test 4 – Password reveal is blocked for unauthorised users; authorised
    //          access succeeds and is audit-logged
    // ──────────────────────────────────────────────────────────────────────────

    /** @test */
    public function password_reveal_is_blocked_and_logs_authorised_access(): void
    {
        $admin     = $this->makeAdmin();
        $member    = $this->makeMember();
        $outsider  = $this->makeMember();  // member with NO project access

        $project = $this->makeProject($admin);
        $project->users()->attach($member->id, ['role' => 'member']);
        // NOTE: $outsider is NOT attached

        $entry = PasswordEntry::create([
            'title'              => 'Test Vault Entry',
            'username'           => 'vault-user',
            'password_encrypted' => Crypt::encryptString('s3cretPa$$'),
            'url'                => 'https://example.com',
            'notes'              => 'Test entry',
            'project_id'         => $project->id,
            'client_id'          => null,
            'created_by'         => $admin->id,
        ]);

        // ── Outsider cannot reveal ──────────────────────────────────────────
        $denied = $this->actingAs($outsider)
            ->postJson(route('passwords.reveal', $entry));

        $denied->assertForbidden();

        // No access log should exist yet
        $this->assertDatabaseMissing('password_entry_access_logs', [
            'password_entry_id' => $entry->id,
        ]);

        // ── Member who IS assigned CAN reveal ─────────────────────────────
        $allowed = $this->actingAs($member)
            ->postJson(route('passwords.reveal', $entry));

        $allowed->assertOk()
            ->assertJsonStructure(['password']);

        $this->assertEquals('s3cretPa$$', $allowed->json('password'));

        // An audit log row should exist for the member
        $this->assertDatabaseHas('password_entry_access_logs', [
            'password_entry_id' => $entry->id,
            'user_id'           => $member->id,
            'action'            => 'reveal',
        ]);
    }
}
