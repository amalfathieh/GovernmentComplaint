<?php

namespace App\Observers;

use App\Events\ComplaintUpdated;
use App\Models\Complaint;
use App\Models\ComplaintHistory;
use App\Services\Admin\AuditService;
use App\Traits\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComplaintObserver
{
    use AuditLog;
    /**
     * Handle the Complaint "created" event.
     */
    public function created(Complaint $complaint): void
    {
        $this->auditLog(action: 'created_complaint',
            model: 'Complaint',
            modelId: $complaint->id);

    }

    /**
     * Handle the Complaint "updated" event.
     */
    public function updated(Complaint $complaint)
    {
        $this->auditLog(
            action: 'updated_complaint_record',
            model: 'Complaint',
            modelId: $complaint->id,
            data: json_encode($complaint->getChanges())
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
