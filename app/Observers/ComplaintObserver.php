<?php

namespace App\Observers;

use App\Events\ComplaintUpdated;
use App\Models\Complaint;
use App\Models\ComplaintHistory;
use App\Services\Admin\AuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComplaintObserver
{
    /**
     * Handle the Complaint "created" event.
     */
    public function created(Complaint $complaint): void
    {
        AuditService::log(
            action: 'created_complaint',
            model: 'Complaint',
            modelId: $complaint->id,
        );

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

        AuditService::log(
            action: 'updated_complaint',
            model: 'Complaint',
            modelId: $complaint->id
        );


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
