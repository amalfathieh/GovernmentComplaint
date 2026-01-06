<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintRequest;
use App\Http\Responses\Response;
use App\Models\Complaint;
use App\Repositories\CitizenComplaint\CitizenComplaintServiceInterface;
// use App\Services\CitizenComplaintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CitizenComplaintController extends Controller
{
    private CitizenComplaintServiceInterface $citizenComplaintService;

    public function __construct(CitizenComplaintServiceInterface $citizenComplaintService)
    {
        $this->citizenComplaintService = $citizenComplaintService;
    }

    public function store(StoreComplaintRequest $request)
    {
        try {
            return $this->citizenComplaintService->createComplaint($request->validated() + [
                    'attachments' => $request->file('attachments') ?: []
                ]);

        } catch (\Exception $exception) {
            Log::error('Complaint create failed', ['exception' => $exception]);
            return Response::Error('Unexpected error: ' . $exception->getMessage(), 500);

        }
    }

    // my complaints (citizen)
    public function myComplaints()
    {
        try {
            return $this->citizenComplaintService->myComplaints(Auth::id());

        }catch (\Exception $exception) {
            Log::error('Complaint get failed', ['exception' => $exception]);
            return Response::Error('Unexpected error: ' . $exception->getMessage(), 500);

        }
    }


    public function update(UpdateComplaintRequest $request, Complaint $complaint)
    {
        try {
            $this->authorize('update', $complaint);

            return $this->citizenComplaintService->update($complaint, $request->validated() + [
                    'attachments' => $request->file('attachments') ?: [],
                    'version_number' => $request->input('version_number'),
                ]);

        } catch (\Exception $e) {
            Log::error('Complaint update failed', [
                'exception' => $e,
                'complaint_id' => $complaint->id,
            ]);
            return Response::Error('Unexpected error: ' . $e->getMessage(), 500);
        }
    }
}
