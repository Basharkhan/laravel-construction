<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class TestimonialController extends Controller {
    public function index() {
        $testimonials = Testimonial::orderBy( 'created_at', 'DESC' )->get();

        return response()->json( [
            'status' => true,
            'data' => $testimonials,
        ] );
    }

    public function store( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'testimonial' => 'required',
            'citation' => 'required',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status' => false,
                'errors' => $validator->errors()
            ] );
        }

        $testimonial = new Testimonial();
        $testimonial->testimonial = $request->testimonial;
        $testimonial->citation = $request->citation;
        $testimonial->status = $request->status;
        $testimonial->save();

        // save temp image
        $imageId = $request->imageId;
        if ( $imageId > 0 ) {
            $tempImage = TempImage::find( $imageId );

            if ( $tempImage != null ) {
                $extArray = explode( '.', $tempImage->name );
                $ext = last( $extArray );
                $fileName = strtotime( 'now' ).$testimonial->id.'.'.$ext;

                // get image from temp
                $sourcePath = public_path( 'uploads/temp/'. $tempImage->name );

                // create small thumbnail
                $destPath = public_path( 'uploads/testimonials/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->coverDown( 300, 300 );
                $image->save( $destPath );

                $testimonial->image = $fileName;
                $testimonial->save();
            }
        }

        return response()->json( [
            'status' => true,
            'message' => 'Testimonial added successfully'
        ] );
    }

    public function update( Request $request, string $id ) {
        $testimonial = Testimonial::find( $id );

        if ( $testimonial === null ) {
            return response()->json( [
                'status' => false,
                'message' => 'Testimonial not found'
            ] );
        }

        $validator = Validator::make( $request->all(), [
            'testimonial' => 'required',
            'citation' => 'required',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status' => false,
                'errors' => $validator->errors()
            ] );
        }

        $testimonial->testimonial = $request->testimonial;
        $testimonial->citation = $request->citation;
        $testimonial->status = $request->status;
        $testimonial->save();

        // save temp image
        $imageId = $request->imageId;
        if ( $imageId > 0 ) {
            $tempImage = TempImage::find( $imageId );
            $oldImage = $testimonial->image;

            if ( $tempImage != null ) {
                $extArray = explode( '.', $tempImage->name );
                $ext = last( $extArray );
                $fileName = strtotime( 'now' ).$testimonial->id.'.'.$ext;

                // get image from temp
                $sourcePath = public_path( 'uploads/temp/'. $tempImage->name );

                // create small thumbnail
                $destPath = public_path( 'uploads/testimonials/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->coverDown( 300, 300 );
                $image->save( $destPath );

                $testimonial->image = $fileName;
                $testimonial->save();

                if ( $oldImage != null ) {
                    File::delete( 'uploads/testimonials/'. $oldImage );

                }
            }
        }

        return response()->json( [
            'status' => true,
            'message' => 'Testimonial updated successfully'
        ] );
    }

    public function show( string $id ) {
        $testimonial = Testimonial::find( $id );

        if ( $testimonial === null ) {
            return response()->json( [
                'status' => false,
                'message' => 'Testimonial not found',
            ] );
        }

        return response()->json( [
            'status' => true,
            'data' => $testimonial,
        ] );
    }

    public function destroy( string $id ) {
        $testimonial = Testimonial::find( $id );

        if ( $testimonial === null ) {
            return response()->json( [
                'status' => false,
                'message' => 'Testimonial not found',
            ] );
        }

        File::delete( 'uploads/projects/small/'. $testimonial->image );

        $testimonial->delete();

        return response()->json( [
            'status' => true,
            'message' => 'Testimonial deleted successfully',
        ] );
    }
}
