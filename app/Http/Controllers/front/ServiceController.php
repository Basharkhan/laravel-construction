<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller {

    // return all active services

    public function index() {
        $services = Service::where( 'status', 1 )->orderBy( 'created_at', 'desc' )->get();
        return $services;
    }

    // return latest active services

    public function latestServices( Request $reuest ) {
        $services = Service::where( 'status', 1 )
        ->take( $reuest->get( 'limit' ) )
        ->orderBy( 'created_at', 'desc' )->get();
        return $services;
    }
}
