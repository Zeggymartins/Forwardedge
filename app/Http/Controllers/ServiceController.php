<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ServiceController extends Controller
{
    public function ServiceList()
    {
        $services = Service::latest()->paginate(6);

        return view('user.pages.service', compact('services'));
    }


    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->with(['contents' => function ($query) {
                $query->orderBy('position', 'asc');
            }])
            ->firstOrFail();

        $otherServices = Service::where('id', '!=', $service->id)->get();

        return view('user.pages.service_details', compact('service', 'otherServices'));
    }



  
}
