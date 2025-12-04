<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class MentionService
{
    public function extractMentions(string $content): Collection
    {
        preg_match_all('/@(\w+)/', $content, $matches);
        
        if (empty($matches[1])) {
            return collect();
        }

        $usernames = array_unique($matches[1]);
        
        return User::whereIn('name', $usernames)
            ->orWhereIn('email', $usernames)
            ->get();
    }

    public function highlightMentions(string $content): string
    {
        return preg_replace(
            '/@(\w+)/',
            '<span class="mention">@$1</span>',
            htmlspecialchars($content)
        );
    }
}

