<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Comment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PasswordEntry;
use App\Models\Project;
use App\Models\SupportPlan;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class CrmSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if already seeded
        if (Client::exists()) {
            return;
        }

        // ── Users ──────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'info@codencio.cz'],
            [
                'name'     => 'Codencio Admin',
                'password' => bcrypt('Xk9m2vLp'),
                'role'     => 'admin',
            ]
        );

        // Aliases kept for seeder references below
        $member1 = $admin;
        $member2 = $admin;

        // ── Clients ────────────────────────────────────────────────────────
        $clients = [
            ['name' => 'Acme Corp',      'email' => 'contact@acme.test',      'phone' => '+1-555-0101', 'company' => 'Acme Corporation',   'address' => '123 Main St, Springfield', 'notes' => 'Key account.'],
            ['name' => 'Globex Inc',     'email' => 'info@globex.test',       'phone' => '+1-555-0202', 'company' => 'Globex Inc.',         'address' => '456 Oak Ave, Shelbyville', 'notes' => 'Referred by Acme.'],
            ['name' => 'Initech LLC',    'email' => 'hello@initech.test',     'phone' => '+1-555-0303', 'company' => 'Initech LLC',         'address' => '789 Pine Rd, Capitol City', 'notes' => 'Focus on TPS reports.'],
        ];

        $clientModels = [];
        foreach ($clients as $data) {
            $clientModels[] = Client::create(array_merge($data, ['created_by' => $admin->id]));
        }

        // ── Projects (2 per client) ────────────────────────────────────────
        $projectData = [
            // Acme
            ['name' => 'Acme Website Redesign',     'description' => 'Full redesign of the Acme corporate site.',   'status' => 'active',   'due_date' => now()->addMonths(2), 'client_idx' => 0],
            ['name' => 'Acme CRM Integration',      'description' => 'Integrate Salesforce with internal ERP.',     'status' => 'planned',  'due_date' => now()->addMonths(4), 'client_idx' => 0],
            // Globex
            ['name' => 'Globex Mobile App',         'description' => 'Cross-platform mobile app for field agents.', 'status' => 'active',   'due_date' => now()->addMonths(3), 'client_idx' => 1],
            ['name' => 'Globex Data Warehouse',     'description' => 'ETL pipeline and BI dashboards.',             'status' => 'on_hold',  'due_date' => now()->addMonths(6), 'client_idx' => 1],
            // Initech
            ['name' => 'Initech TPS Portal',        'description' => 'Online TPS report submission portal.',        'status' => 'done',     'due_date' => now()->subMonth(),   'client_idx' => 2],
            ['name' => 'Initech Employee Intranet', 'description' => 'Internal HR and document management system.', 'status' => 'active',   'due_date' => now()->addMonths(2), 'client_idx' => 2],
        ];

        $projects = [];
        foreach ($projectData as $pd) {
            $client_idx = $pd['client_idx'];
            unset($pd['client_idx']);
            $project = Project::create(array_merge($pd, [
                'client_id'  => $clientModels[$client_idx]->id,
                'created_by' => $admin->id,
            ]));
            // Attach admin as lead
            $project->users()->attach($admin->id, ['role' => 'lead']);
            // Attach both members
            $project->users()->attach($member1->id, ['role' => 'member']);
            $project->users()->attach($member2->id, ['role' => 'member']);

            $projects[] = $project;
        }

        // ── Tasks (3 per project) ──────────────────────────────────────────
        $taskStatuses   = ['todo', 'in_progress', 'review', 'done'];
        $taskPriorities = ['low', 'medium', 'high'];

        $taskTitles = [
            ['Gather requirements', 'Design mockups', 'Deploy to staging'],
            ['API specification',   'Implement backend', 'Write tests'],
            ['UI wireframes',       'Connect REST API', 'QA & bug fixes'],
            ['Schema design',       'Build ETL jobs',   'Dashboard setup'],
            ['Create forms',        'Email notifications', 'Go live checklist'],
            ['Information architecture', 'Content migration', 'SEO audit'],
        ];

        $allTasks = [];
        foreach ($projects as $pi => $project) {
            $titles = $taskTitles[$pi] ?? ['Task A', 'Task B', 'Task C'];
            foreach ($titles as $ti => $title) {
                $task = Task::create([
                    'title'       => $title,
                    'description' => "Description for: {$title}.",
                    'status'      => $taskStatuses[$ti % count($taskStatuses)],
                    'priority'    => $taskPriorities[$ti % count($taskPriorities)],
                    'due_date'    => now()->addDays(rand(5, 30)),
                    'project_id'  => $project->id,
                    'created_by'  => $admin->id,
                ]);
                $task->assignees()->attach($member1->id, ['assigned_by' => $admin->id]);
                $task->assignees()->attach($member2->id, ['assigned_by' => $admin->id]);

                $allTasks[] = $task;
            }
        }

        // ── Comments (2 top-level + 1 reply each per task) ────────────────
        foreach ($allTasks as $task) {
            for ($c = 1; $c <= 2; $c++) {
                $author = $c === 1 ? $member1 : $member2;
                $comment = Comment::create([
                    'body'      => "Comment #{$c} on task \"{$task->title}\". @{$author->name} please review.",
                    'task_id'   => $task->id,
                    'user_id'   => $author->id,
                    'parent_id' => null,
                ]);

                // One reply
                $replier = $c === 1 ? $member2 : $admin;
                Comment::create([
                    'body'      => "Reply to comment #{$c}: looks good, thanks @{$author->name}!",
                    'task_id'   => $task->id,
                    'user_id'   => $replier->id,
                    'parent_id' => $comment->id,
                ]);
            }
        }

        // ── Password Entries (2 per project) ──────────────────────────────
        $services = ['GitHub', 'AWS', 'Figma', 'Jira', 'Slack', 'Notion', 'DigitalOcean', 'Heroku', 'Netlify', 'Vercel', 'Postman', 'Stripe'];
        $si = 0;
        foreach ($projects as $project) {
            for ($p = 0; $p < 2; $p++) {
                $service = $services[$si++ % count($services)];
                PasswordEntry::create([
                    'title'              => "{$service} – {$project->name}",
                    'username'           => 'admin@example.com',
                    'password_encrypted' => Crypt::encryptString('Sup3rS3cr3t!'),
                    'url'                => 'https://' . strtolower($service) . '.com',
                    'notes'              => "Credentials for {$service} on the {$project->name} project.",
                    'project_id'         => $project->id,
                    'client_id'          => $project->client_id,
                    'created_by'         => $admin->id,
                ]);
            }
        }

        // ── Invoices (2 per client ≈ 6 total) ────────────────────────────
        $invoiceStatuses = ['paid', 'sent', 'draft', 'overdue', 'paid', 'sent'];
        foreach ($clientModels as $ci => $client) {
            for ($i = 0; $i < 2; $i++) {
                $inv = Invoice::create([
                    'client_id'      => $client->id,
                    'project_id'     => $projects[$ci * 2 + $i]->id ?? null,
                    'invoice_number' => Invoice::generateNumber(),
                    'issued_at'      => now()->subDays(rand(10, 60)),
                    'due_at'         => now()->addDays(rand(-5, 30)),
                    'paid_at'        => $invoiceStatuses[$ci * 2 + $i] === 'paid' ? now()->subDays(rand(1, 10)) : null,
                    'status'         => $invoiceStatuses[$ci * 2 + $i],
                    'currency'       => 'CZK',
                    'tax_rate'       => 21,
                    'notes'          => "Faktura pro {$client->name}.",
                    'created_by'     => $admin->id,
                    'subtotal'       => 0,
                    'tax_amount'     => 0,
                    'total'          => 0,
                ]);

                // 2-3 items per invoice
                $itemCount = rand(2, 3);
                for ($j = 1; $j <= $itemCount; $j++) {
                    InvoiceItem::create([
                        'invoice_id' => $inv->id,
                        'name'       => "Služba #{$j}",
                        'description'=> "Popis položky #{$j}",
                        'qty'        => rand(1, 20),
                        'unit_price' => rand(500, 5000),
                        'sort_order' => $j,
                    ]);
                }

                $inv->recalculate();
            }
        }

        // ── Support Plans (1 per client) ─────────────────────────────────
        foreach ($clientModels as $ci => $client) {
            SupportPlan::create([
                'client_id'   => $client->id,
                'title'       => "Měsíční podpora – {$client->name}",
                'price'       => rand(5, 30) * 1000,
                'currency'    => 'CZK',
                'period_from' => now()->subMonths(rand(1, 6)),
                'period_to'   => now()->addMonths(rand(1, 6)),
                'status'      => $ci < 2 ? 'active' : 'expired',
                'notes'       => "Podpůrný plán pro {$client->name}.",
                'created_by'  => $admin->id,
            ]);
        }
    }
}
