<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Models\User;
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    public function index(){
        try {
            $users = User::where('role', 'user')->get();
            return Response::Success($users, 'All Citizens');

        } catch (\Exception $e) {
            return Response::Error($e->getMessage(), 423);
        }
    }


}
