<?php

namespace App\Listeners;

use App\Events\ComplaintUpdated;
use App\Models\ComplaintHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $newVersion = $event->complaint->version_number + 1;

        DB::transaction(function () use ($event, $newVersion) {
            \App\Models\ComplaintHistory::create([
                'complaint_id' => $event->complaint->id,
                'user_id'      => $event->userId,
                'action'       => $event->action,
                'old_snapshot' => $event->oldSnapshot,
            ]);

            // تحديث رقم النسخة بدون إطلاق الـ Observer
            $event->complaint->updateQuietly([
                'version_number' => $newVersion,
            ]);
        });
    }
}
