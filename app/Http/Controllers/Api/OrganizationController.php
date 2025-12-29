<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OrganizationController extends Controller
{
    public function getOrganizations()
    {
        return Cache::remember('organizations', now()->addHour(), function () {

            $organizations = Organization::all();
            return Response::Success($organizations, 'success');
        });

    }
}
