<?php


namespace App\Services;


use App\Http\Responses\Response;
use App\Models\Complaint;
use App\Models\User;

class AdminService
{
    public function statistic(){

        $citizensCount = User::where('role','user')->count();
        $employeesCount = User::where('role','employee')->count();
        $complaintsCount = Complaint::count();
        $complaintsResolved = Complaint::where('status','resolved')->count();
        $complaintsNew = Complaint::where('status','new')->count();
        $complaintsRejected = Complaint::where('status','rejected')->count();
        $data = [
            'usersCount' => $citizensCount,
            'employeesCount' => $employeesCount,
            'complaintsCount' => $complaintsCount,
            'complaintsSolved' =>$complaintsResolved,
            'complaintsNew' => $complaintsNew,
            'complaintsRejected' => $complaintsRejected,
        ];
        return $data;
    }
}
