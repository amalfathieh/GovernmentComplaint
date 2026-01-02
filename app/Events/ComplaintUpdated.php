<?php

namespace App\Events;

use App\Models\Complaint;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComplaintUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $oldSnapshot;
    public $complaint;
    public $userId;
    public $action;

    public function __construct($oldSnapshot, $complaint, $userId, $action = 'updated')
    {
        $this->oldSnapshot = $oldSnapshot;
        $this->complaint = $complaint;
        $this->userId = $userId;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
    }
}
