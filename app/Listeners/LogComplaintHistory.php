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

        // create full snapshot history
        ComplaintHistory::create([
            'complaint_id' =>$event->oldSnapshot->id,
            'user_id' => Auth::id(),
            'action' => 'updated',
            'old_snapshot' => $event->oldSnapshot,
            'new_snapshot' => $event->newSnapshot,
        ]);

    }
}
