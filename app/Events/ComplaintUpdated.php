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

    public $old;
    public $new;
    public $complaintId;
    public $userId;

    public function __construct($old, $new, $complaintId, $userId)
    {
        $this->old = $old;
        $this->new = $new;
        $this->complaintId = $complaintId;
        $this->userId = $userId;
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
