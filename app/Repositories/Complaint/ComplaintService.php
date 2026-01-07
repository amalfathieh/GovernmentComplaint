<?php


namespace App\Repositories\Complaint;


use App\Events\ComplaintUpdated;
use App\Jobs\SendComplaintNotification;
use App\Models\Complaint;
use App\Support\ComplaintCache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ComplaintService implements ComplaintServiceInterface
{
    private int $lockMinutes = 10;


    public function update(Complaint $complaint, $status = null, $note = null)
    {
        $userId = Auth::id();

        $complaint = Complaint::where('id', $complaint->id)
            ->lockForUpdate()
            ->first();

        // ensure lock held by this user (or expired)
        if ($complaint->lockedByAnotherUser($userId)) {
            throw new \RuntimeException('Complaint is locked by another user.');
        }
        $oldSnapshot = $complaint->toArray();
        $oldSnapshot['attachments'] = $complaint->attachments->toArray();

        $complaint->update([
            'status' => $status ?? $complaint->status,
            'note' => $note ?? $complaint->note,
        ]);

        if ($complaint->wasChanged()) {
            event(new ComplaintUpdated($oldSnapshot, $complaint, Auth::id(), 'status_changed'));
            ComplaintCache::bump();
        }
        //send notification to user
        SendComplaintNotification::dispatch($complaint, $complaint->user);

        Cache::forget("my_complaints_user_{$complaint->user_id}");
        Cache::forget("employee_{$complaint->locked_by}_complaints");

        return $complaint->fresh();
    }

    // حجز شكوى للمعالجة
    public function lock(Complaint $complaint)
    {

        $complaint = Complaint::where('id', $complaint->id)->lockForUpdate()->first();

        if ($complaint->lockedByAnotherUser(Auth::id())) {
            throw new \RuntimeException('الشكوى قيد المعالجة من قبل مستخدم آخر');
        }

        $complaint->update([
            'locked_by' => Auth::id(),
            'locked_until' => now()->addMinutes($this->lockMinutes),
        ]);
        return true;
    }

    public function unlock(Complaint $complaint): bool
    {
        $complaint = Complaint::where('id', $complaint->id)->lockForUpdate()->first();

        // allow unlock if same user or expired
        if ($complaint->lockedByAnotherUser(Auth::id())) {
            throw new \RuntimeException('Cannot unlock, locked by another user.');
        }

        $complaint->update([
            'locked_by' => null,
            'locked_until' => null,
        ]);

        return true;
    }

    public function allComplaintForAdmin($request)
    {
        $version = \App\Support\ComplaintCache::version();

        // 1. ترتيب الفلاتر لضمان ثبات المفتاح
        $params = $request->query();
        ksort($params);

        // 2. المفتاح يحتوي على النسخة + بصمة الفلترة
        $key = "admin_complaints_v{$version}_" . md5(json_encode($params));

        return Cache::remember($key, now()->addMinutes(10), function () use ($params) {
            return Complaint::with([
                'user:id,first_name,last_name',
                'attachments:id,complaint_id,file_path,file_type',
                'histories.user:id,first_name,last_name,role',
                'organization:id,name'
            ])
                ->filterStatus($params) // تأكدي من تمرير $params كاملة
                ->filterOrganization($params)
                ->latest()
                ->paginate(20);
        });
    }

    public function allComplaintForEmployee($request)
    {
        $user = Auth::user();
        $version = ComplaintCache::version();

        // ترتيب البارامترات لضمان دقة الكاش عند الفلترة (مثلاً حسب الحالة)
        $params = $request->query();
        ksort($params);

        $key = "employee_{$user->id}_v{$version}_" . md5(json_encode($params));

        return Cache::remember($key, now()->addMinutes(5), function () use ($params, $user) {
            return Complaint::with([
                'user:id,first_name,last_name',
                'attachments',
                'histories.user:id,first_name,last_name,role',
            ])
                ->filterStatus($params)
                ->where('organization_id', $user->organization_id)
                ->latest()
                ->paginate(20);
        });
    }

}
