<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function ServiceList()
    {
        $services = Service::all();

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
