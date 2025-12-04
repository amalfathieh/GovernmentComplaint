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
        // 2. زيادة رقم النسخة الحالية للشكوى
        $newVersion = $event->complaint->version_number + 1;

//        dd($newVersion);
        DB::transaction(function () use ($event, $newVersion) {

            // 3. حفظ اللقطة القديمة كـ "النسخة"
            ComplaintHistory::create([
                'complaint_id' => $event->complaint->id,
                'user_id' => Auth::check() ? Auth::id() : null,
                'action' => 'updated',
                'old_snapshot' => $event->old, // اللقطة الكاملة للنسخة السابقة
            ]);

            $event->complaint->updateQuietly([
                'version_number' => $newVersion,
            ]);
        });

    }
}
