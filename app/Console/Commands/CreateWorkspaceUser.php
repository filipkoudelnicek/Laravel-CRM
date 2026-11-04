<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateWorkspaceUser extends Command
{
    protected $signature = 'crm:user {email?} {--admin : Účet administrátora}';
    protected $description = 'Vytvoří uživatele CRM s vlastním heslem.';

    public function handle(): int
    {
        $data = ['email'=>$this->argument('email') ?: $this->ask('E-mail'),
            'name'=>$this->ask('Jméno'), 'password'=>$this->secret('Heslo (alespoň 12 znaků)'),
            'password_confirmation'=>$this->secret('Zopakujte heslo')];
        $validator = Validator::make($data, ['email'=>'required|email|unique:users,email',
            'name'=>'required|string|max:255','password'=>'required|string|min:12|confirmed']);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) $this->error($error);
            return self::FAILURE;
        }
        User::create(['name'=>$data['name'],'email'=>$data['email'],'password'=>$data['password'],
            'role'=>$this->option('admin') ? 'admin' : 'member']);
        $this->info('Účet vytvořen.');
        return self::SUCCESS;
    }
}
