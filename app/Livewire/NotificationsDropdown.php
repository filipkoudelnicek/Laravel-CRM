<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;

class NotificationsDropdown extends Component
{
    public $showDropdown = false;
    public $notifications = [];
    public $unreadCount = 0;

    public function mount()
    {
        if (auth()->check()) {
            $this->loadUnreadCount();
        }
    }
    
    public function hydrate()
    {
        // Při hydrataci se znovu načte počet, ale bez zbytečných dotazů
        if (auth()->check()) {
            $this->loadUnreadCount();
        }
    }

    public function toggleDropdown()
    {
        if (!$this->showDropdown) {
            $this->loadNotifications();
        }
        $this->showDropdown = !$this->showDropdown;
    }

    public function loadUnreadCount()
    {
        if (!auth()->check()) {
            $this->unreadCount = 0;
            return;
        }

        $this->unreadCount = Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->count();
    }

    public function loadNotifications()
    {
        if (!auth()->check()) {
            $this->notifications = [];
            return;
        }

        $this->notifications = Notification::where('user_id', auth()->id())
            ->with('task')
            ->latest()
            ->take(20)
            ->get()
            ->toArray();
    }

    public function markAsRead($notificationId)
    {
        if (!auth()->check()) {
            return;
        }

        Notification::where('id', $notificationId)
            ->where('user_id', auth()->id())
            ->update(['read' => true]);

        $this->loadUnreadCount();
        $this->loadNotifications();
    }

    public function markAllAsRead()
    {
        if (!auth()->check()) {
            return;
        }

        Notification::where('user_id', auth()->id())
            ->update(['read' => true]);

        $this->loadUnreadCount();
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notifications-dropdown');
    }
}

