<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

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

        // save temp image
        $imageId = $request->imageId;
        if ( $imageId > 0 ) {
            $tempImage = TempImage::find( $imageId );

            if ( $tempImage != null ) {
                $extArray = explode( '.', $tempImage );
                $ext = last( $extArray );
                $fileName = strtotime( 'now' ).$service->id.'.'.$ext;

                // get image from temp
                $sourcePath = public_path( 'uploads/temp/'. $tempImage->name );

                // create small thumbnail
                $destPath = public_path( 'uploads/services/small/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->coverDown( 500, 600 );
                $image->save( $destPath );

                // create large thumbnail
                $destPath = public_path( 'uploads/services/large/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->scaleDown( 1200 );
                $image->save( $destPath );

                $service->image = $fileName;
                $service->save();
            }
        }

        return response()->json( [
            'status' => true,
            'message' => 'Service added successfully'
        ] );
    }

    /**
    * Display the specified resource.
    */

    public function show( string $id ) {
        $service = Service::find( $id );

        if ( $service === null ) {
            return response()->json( [
                'status' => false,
                'message' => 'Service not found',
            ] );
        }

        return response()->json( [
            'status' => true,
            'message' => $service,
        ] );
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
        $service = Service::find( $id );

        if ( $service === null ) {
            return response()->json( [
                'status' => false,
                'message' => 'Service not found'
            ] );
        }

        $validator = Validator::make( $request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:services,slug,'.$id.',id'
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status' => false,
                'errors' => $validator->errors()
            ] );
        }

        $service->title = $request->title;
        $service->slug = Str::slug( $request->slug );
        $service->short_desc = $request->short_desc;
        $service->content = $request->content;
        $service->status = $request->status;
        $service->save();

        // save temp image
        $imageId = $request->imageId;
        if ( $imageId > 0 ) {
            $tempImage = TempImage::find( $imageId );

            if ( $tempImage != null ) {
                $extArray = explode( '.', $tempImage );
                $ext = last( $extArray );
                $fileName = strtotime( 'now' ).$service->id.'.'.$ext;

                // get image from temp
                $sourcePath = public_path( 'uploads/temp/'. $tempImage->name );

                // create small thumbnail
                $destPath = public_path( 'uploads/services/small/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->coverDown( 500, 600 );
                $image->save( $destPath );

                // create large thumbnail
                $destPath = public_path( 'uploads/services/large/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->scaleDown( 1200 );
                $image->save( $destPath );

                $service->image = $fileName;
                $service->save();

                $oldImage = $service->image;
                if ( $oldImage != null ) {
                    File::delete( 'uploads/services/small/'. $oldImage );
                    File::delete( 'uploads/services/large/'. $oldImage );
                }
            }
        }

        return response()->json( [
            'status' => true,
            'message' => 'Service updated successfully'
        ] );
    }

    /**
    * Remove the specified resource from storage.
    */

    public function destroy( string $id ) {
        $service = Service::find( $id );

        if ( $service === null ) {
            return response()->json( [
                'status' => false,
                'message' => 'Service not found',
            ] );
        }

        $service->delete();

        return response()->json( [
            'status' => true,
            'message' => 'Service deleted successfully',
        ] );
    }
}
