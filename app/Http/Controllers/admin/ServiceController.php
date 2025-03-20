<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceController extends Controller {
    /**
    * Display a listing of the resource.
    */

    public function index() {
        $services = Service::orderBy( 'created_at', 'DESC' )->get();

        return response()->json( [
            'status' => true,
            'data' => $services,
        ] );
    }

    /**
    * Show the form for creating a new resource.
    */

    public function create() {
        //
    }

    /**
    * Store a newly created resource in storage.
    */

    public function store( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:services,slug',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status' => false,
                'errors' => $validator->errors()
            ] );
        }

        $service = new Service();
        $service->title = $request->title;
        $service->slug = Str::slug( $request->slug );
        $service->short_desc = $request->short_desc;
        $service->content = $request->content;
        $service->status = $request->status;
        $service->save();

        return response()->json( [
            'status' => true,
            'message' => 'Service added successfully'
        ] );
    }

    /**
    * Display the specified resource.
    */

    public function show( string $id ) {
        //
    }

    /**
    * Show the form for editing the specified resource.
    */

    public function edit( string $id ) {
        //
    }

    /**
    * Update the specified resource in storage.
    */

    public function update( Request $request, string $id ) {
        //
    }

    /**
    * Remove the specified resource from storage.
    */

    public function destroy( string $id ) {
        //
    }
}
