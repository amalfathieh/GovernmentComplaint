<?php

namespace App\Observers;

use App\Events\ComplaintUpdated;
use App\Models\Complaint;
use App\Models\ComplaintHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComplaintObserver
{
    /**
     * Handle the Complaint "created" event.
     */
    public function created(Complaint $complaint): void
    {
        //
    }

    /**
     * Handle the Complaint "updated" event.
     */
    public function updated(Complaint $complaint)
    {

        $excludedFields = ['locked_by', 'locked_until', 'updated_at', 'version_number'];

        $changedAttributes = collect($complaint->getDirty())->except($excludedFields);

        // إذا لم يتغير شيء جوهري، نتوقف.
        if ($changedAttributes->isEmpty()) {
            return;
        }

        event(new ComplaintUpdated(
            $complaint->getOriginal(),
            $complaint,
            Auth::id()
        ));

    }

    /**
     * Handle the Complaint "deleted" event.
     */
    public function deleted(Complaint $complaint): void
    {
        //
    }

    /**
     * Handle the Complaint "restored" event.
     */
    public function restored(Complaint $complaint): void
    {
        //
    }

    /**
     * Handle the Complaint "force deleted" event.
     */
    public function forceDeleted(Complaint $complaint): void
    {
        //
    }
}
