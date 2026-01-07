<?php


namespace App\Services\Admin;


use App\Http\Responses\Response;
use App\Models\Complaint;
use App\Models\User;
use App\Support\ComplaintCache;
use Illuminate\Support\Facades\Cache;

class AdminService
{
    public function statistic()
    {

        $version = ComplaintCache::version();
        return Cache::remember("admin_stats_v{$version}", now()->addMinutes(10), function () {
            return [
                'citizensCount'       => User::where('role', 'user')->count(),
                'employeesCount'      => User::where('role', 'employee')->count(),
                'complaintsCount'     => Complaint::count(),
                'complaintsResolved'  => Complaint::where('status', 'resolved')->count(),
                'complaintsNew'       => Complaint::where('status', 'new')->count(),
                'complaintsRejected'  => Complaint::where('status', 'rejected')->count(),
            ];
        });
    }
}
