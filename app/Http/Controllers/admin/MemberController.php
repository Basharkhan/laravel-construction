<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class MemberController extends Controller {
    public function index() {
        $members = Member::where( 'status', 1 )->orderBy( 'id', 'desc' )->get();
        return response()->json( [
            'status' => true,
            'data' => $members
        ] );
    }

    public function store( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'name' => 'required',
            'job_title' => 'required',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status' => false,
                'errors' => $validator->errors()
            ] );
        }

        $member = new Member();
        $member->name = $request->name;
        $member->job_title = $request->job_title;
        $member->linkin_url = $request->linkin_url;
        $member->status = $request->status;
        $member->save();

        // save temp image
        $imageId = $request->imageId;
        if ( $imageId > 0 ) {
            $tempImage = TempImage::find( $imageId );

            if ( $tempImage != null ) {
                $extArray = explode( '.', $tempImage->name );
                $ext = last( $extArray );
                $fileName = strtotime( 'now' ).$member->id.'.'.$ext;

                // get image from temp
                $sourcePath = public_path( 'uploads/temp/'. $tempImage->name );

                // create small thumbnail
                $destPath = public_path( 'uploads/members/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->coverDown( 400, 500 );
                $image->save( $destPath );

                $member->image = $fileName;
                $member->save();
            }
        }

        return response()->json( [
            'status' => true,
            'message' => 'Member added successfully'
        ] );
    }

    public function update( Request $request, $id ) {
        $member = Member::find( $id );

        if ( $member == null ) {
            return response()->json( [
                'status' => false,
                'message' => 'Member not found',
            ] );
        }

        $validator = Validator::make( $request->all(), [
            'name' => 'required',
            'job_title' => 'required',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status' => false,
                'errors' => $validator->errors()
            ] );
        }

        $member->name = $request->name;
        $member->job_title = $request->job_title;
        $member->linkin_url = $request->linkin_url;
        $member->status = $request->status;
        $member->save();

        // save temp image
        $imageId = $request->imageId;
        if ( $imageId > 0 ) {
            $oldImage = $member->image;
            $tempImage = TempImage::find( $imageId );

            if ( $tempImage != null ) {
                $extArray = explode( '.', $tempImage->name );
                $ext = last( $extArray );
                $fileName = strtotime( 'now' ).$member->id.'.'.$ext;

                // get image from temp
                $sourcePath = public_path( 'uploads/temp/'. $tempImage->name );

                // create small thumbnail
                $destPath = public_path( 'uploads/members/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->coverDown( 400, 500 );
                $image->save( $destPath );

                $member->image = $fileName;
                $member->save();

                if ( $oldImage != null ) {
                    File::delete( 'uploads/members/'. $oldImage );

                }
            }
        }

        return response()->json( [
            'status' => true,
            'message' => 'Member updated successfully'
        ] );
    }

    public function show( string $id ) {
        $member = Member::find( $id );

        if ( $member === null ) {
            return response()->json( [
                'status' => false,
                'message' => 'Member not found',
            ] );
        }

        return response()->json( [
            'status' => true,
            'data' => $member,
        ] );
    }

    public function destroy( string $id ) {
        $member = Member::find( $id );

        if ( $member === null ) {
            return response()->json( [
                'status' => false,
                'message' => 'Member not found',
            ] );
        }

        File::delete( 'uploads/members/'. $member->image );

        $member->delete();

        return response()->json( [
            'status' => true,
            'message' => 'Member deleted successfully',
        ] );
    }
}
