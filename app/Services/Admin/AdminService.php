<?php


namespace App\Services\Admin;


use App\Http\Responses\Response;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AdminService
{
    public function statistic()
    {

        return Cache::remember('admin_stats', now()->addMinutes(5), function () {
            $data = [];
            $data['citizensCount'] = User::where('role', 'user')->count();
            $data['employeesCount'] = User::where('role', 'employee')->count();
            $data['complaintsCount'] = Complaint::count();
            $data['complaintsResolved'] = Complaint::where('status', 'resolved')->count();
            $data['complaintsNew'] = Complaint::where('status', 'new')->count();
            $data['complaintsRejected'] = Complaint::where('status', 'rejected')->count();
            return $data;
        });
    }
}
