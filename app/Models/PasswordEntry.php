<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PasswordEntry extends Model
{
    use HasFactory;

    public const TYPES = [
        'general' => 'Obecné heslo',
        'sftp' => 'SFTP',
        'admin' => 'Administrace',
        'hosting' => 'Hosting',
    ];

    protected $fillable = [
        'title', 'username', 'password_encrypted', 'url', 'notes',
        'type', 'client_id', 'project_id', 'created_by',
    ];

    // Never expose the encrypted value accidentally
    protected $hidden = ['password_encrypted'];

    // ── accessors / mutators ──────────────────────────────────

    public function setPasswordAttribute(string $plaintext): void
    {
        $this->attributes['password_encrypted'] = Crypt::encryptString($plaintext);
    }

    public function getDecryptedPassword(): string
    {
        return Crypt::decryptString($this->password_encrypted);
    }

    // ── relationships ─────────────────────────────────────────

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function accessLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PasswordEntryAccessLog::class)->orderByDesc('created_at');
    }
}
