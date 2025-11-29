<?php

namespace App\Listeners;

use App\Events\ComplaintUpdated;
use App\Models\ComplaintHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogComplaintHistory
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ComplaintUpdated $event): void
    {
        ComplaintHistory::create([
            'complaint_id' => $event->complaintId,
            'user_id' => $event->userId,
            'action' => 'updated',
            'old_snapshot' => $event->old,
            'new_snapshot' => $event->new,
        ]);

    }
}
