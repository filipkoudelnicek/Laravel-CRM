<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class PasswordEncryptionService
{
    public function encrypt(string $password): string
    {
        return Crypt::encryptString($password);
    }

    public function decrypt(string $encryptedPassword): string
    {
        return Crypt::decryptString($encryptedPassword);
    }
}

