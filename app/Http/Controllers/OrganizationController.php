<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function getOrganizations(){
        $organizations = Organization::all();
        return Response::Success($organizations, 'success');

    }
}
