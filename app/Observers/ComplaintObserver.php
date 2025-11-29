<?php

namespace App\Observers;

use App\Events\ComplaintUpdated;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;

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
//    public function updated(Complaint $complaint): void
//    {
//        $oldSnapshot = $complaint->getOriginal();
//        $newSnapshot = $complaint->toArray();
//        event(new ComplaintUpdated($oldSnapshot, $newSnapshot));
//    }
    public function updated(Complaint $complaint)
    {
        event(new ComplaintUpdated(
            $complaint->getOriginal(),
            $complaint->getAttributes(),
            $complaint->id,
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
