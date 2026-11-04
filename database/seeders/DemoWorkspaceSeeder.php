<?php

namespace Database\Seeders;

use App\Models\{Client, Invoice, Project, SupportPlan, Task, TimeEntry, User};
use Illuminate\Database\Seeder;

class DemoWorkspaceSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Ukázková data lze vytvořit pouze v local/testing prostředí.');
        }
        if (Client::exists()) return;

        $adam = User::firstOrCreate(['email'=>'dev1@example.test'],
            ['name'=>'Adam Novák','password'=>'Local-preview-2026!','role'=>'admin']);
        $petr = User::firstOrCreate(['email'=>'dev2@example.test'],
            ['name'=>'Petr Malý','password'=>'Local-preview-2026!','role'=>'member']);
        $clients = collect([
            ['name'=>'Studio Forma','company'=>'Studio Forma s.r.o.','email'=>'studio@example.test','notes'=>'Nasazení domlouváme předem. Obsah schvaluje Klára.'],
            ['name'=>'Novotný & syn','company'=>'Novotný & syn s.r.o.','email'=>'obchod@example.test','notes'=>'Měsíční správa webu a drobné úpravy.'],
        ])->map(fn($data)=>Client::create($data+['created_by'=>$adam->id]));

        $projects = collect([
            ['name'=>'Nový web studia','description'=>'Prezentace architektonického studia. Aktuálně ladíme portfolio a kontaktní formulář.','client_id'=>$clients[0]->id],
            ['name'=>'Klientský portál','description'=>'Přehled objednávek a dokumentů pro zákazníky.','client_id'=>$clients[1]->id],
            ['name'=>'Průběžná správa webu','description'=>'Drobné úpravy, aktualizace a provoz webu.','client_id'=>$clients[1]->id],
        ])->map(function($data) use ($adam,$petr) {
            $project=Project::create($data+['status'=>'active','due_date'=>today()->addDays(14),'created_by'=>$adam->id]);
            $project->users()->attach([$adam->id=>['role'=>'lead'],$petr->id=>['role'=>'member']]);
            return $project;
        });
        $specs = [
            ['Doladit mobilní portfolio','in_progress',$projects[0],$adam,-1],
            ['Ověřit odesílání poptávek','review',$projects[0],$petr,1],
            ['Připravit import objednávek','todo',$projects[1],$adam,2],
            ['Zapracovat připomínky k přihlášení','in_progress',$projects[1],$petr,3],
            ['Aktualizovat závislosti webu','todo',$projects[2],$adam,5],
        ];
        foreach($specs as [$title,$status,$project,$person,$days]) {
            $task=Task::create(['title'=>$title,'description'=>'<p>Dokončit úpravu a předat kolegovi ke kontrole.</p>',
                'project_id'=>$project->id,'created_by'=>$adam->id,'status'=>$status,'priority'=>'medium',
                'starts_at'=>today()->subDays(2),'due_at'=>today()->addDays($days)]);
            $task->assignees()->attach($person->id,['assigned_by'=>$adam->id]);
            TimeEntry::create(['task_id'=>$task->id,'created_by'=>$person->id,
                'started_at'=>now()->subHours(4),'ended_at'=>now()->subHours(2),'notes'=>'Implementace a kontrola']);
        }
        $projects[0]->notes()->create(['user_id'=>$petr->id,'body'=>"Klára schválila strukturu portfolia.\nZbývá doplnit dvě realizace a ověřit mobilní náhled.",'created_at'=>now()->subHours(2)]);
        $projects[1]->notes()->create(['user_id'=>$adam->id,'body'=>'Import objednávek nejdřív spustíme nad testovacími daty. Se zákazníkem projdeme výsledek v pátek.','created_at'=>now()->subHour()]);
        foreach([['Hosting a zálohy',$clients[0],$projects[0],14,1500,true],['Měsíční správa webu',$clients[1],$projects[2],7,2900,true],['Provoz klientského portálu',$clients[1],$projects[1],25,1800,false]] as [$title,$client,$project,$days,$price,$paid]) {
            SupportPlan::create(['title'=>$title,'client_id'=>$client->id,'project_id'=>$project->id,'created_by'=>$adam->id,
                'price'=>$price,'currency'=>'CZK','period_from'=>today()->subMonths(11),'period_to'=>today()->addDays($days),
                'paid_at'=>$paid ? today()->subMonths(11) : null,'status'=>'active']);
        }
        $invoice=Invoice::create(['client_id'=>$clients[0]->id,'created_by'=>$adam->id,'invoice_number'=>Invoice::generateNumber(),
            'status'=>'paid','currency'=>'CZK','tax_rate'=>21,'issued_at'=>today()->subDays(10),
            'due_at'=>today()->addDays(4),'paid_at'=>today()->subDays(3)]);
        $invoice->items()->create(['name'=>'Vývoj webu','qty'=>10,'unit_price'=>1200,'sort_order'=>0]);
        $invoice->recalculate();
    }
}
