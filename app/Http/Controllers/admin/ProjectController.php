<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\File;

class ProjectController extends Controller {
    // get all projects

    public function index() {
        $projects = Project::orderBy( 'created_at', 'DESC' )->get();

        return response()->json( [
            'status' => true,
            'data' => $projects,
        ] );
    }

    public function store( Request $request ) {
        $request->merge( [ 'slug' => Str::slug( $request->slug ) ] );

        $validator = Validator::make( $request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:projects,slug',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status' => false,
                'message' => $validator->errors(),
            ] );
        }

        $project = new Project();
        $project->title = $request->title;
        $project->slug = Str::slug( $request->slug );
        $project->short_desc = $request->short_desc;
        $project->content = $request->content;
        $project->construction_type = $request->construction_type;
        $project->sector = $request->sector;
        $project->status = $request->status;
        $project->location = $request->location;

        $project->save();

        // save temp image
        $imageId = $request->imageId;
        if ( $imageId > 0 ) {
            $tempImage = TempImage::find( $imageId );

            if ( $tempImage != null ) {
                $extArray = explode( '.', $tempImage->name );
                $ext = last( $extArray );
                $fileName = strtotime( 'now' ).$project->id.'.'.$ext;

                // get image from temp
                $sourcePath = public_path( 'uploads/temp/'. $tempImage->name );

                // create small thumbnail
                $destPath = public_path( 'uploads/projects/small/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->coverDown( 500, 600 );
                $image->save( $destPath );

                // create large thumbnail
                $destPath = public_path( 'uploads/projects/large/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->scaleDown( 1200 );
                $image->save( $destPath );

                $project->image = $fileName;
                $project->save();
            }
        }

        return response()->json( [
            'status' => true,
            'message' => 'Project created successfully',
        ] );
    }

    public function update( Request $request, $id ) {
        $project = Project::find( $id );

        if ( $project == null ) {
            return response()->json( [
                'status' => false,
                'message' => 'Project not found',
            ] );
        }

        $request->merge( [ 'slug' => Str::slug( $request->slug ) ] );

        $validator = Validator::make( $request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:projects,slug,'.$id.',id'
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status' => false,
                'message' => $validator->errors(),
            ] );
        }

        $project->title = $request->title;
        $project->slug = Str::slug( $request->slug );
        $project->short_desc = $request->short_desc;
        $project->content = $request->content;
        $project->construction_type = $request->construction_type;
        $project->sector = $request->sector;
        $project->status = $request->status;
        $project->location = $request->location;

        $project->save();

        // save temp image
        $imageId = $request->imageId;
        if ( $imageId > 0 ) {
            $oldImage = $project->image;
            $tempImage = TempImage::find( $imageId );

            if ( $tempImage != null ) {
                $extArray = explode( '.', $tempImage->name );
                $ext = last( $extArray );
                $fileName = strtotime( 'now' ).$project->id.'.'.$ext;

                // get image from temp
                $sourcePath = public_path( 'uploads/temp/'. $tempImage->name );

                // create small thumbnail
                $destPath = public_path( 'uploads/projects/small/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->coverDown( 500, 600 );
                $image->save( $destPath );

                // create large thumbnail
                $destPath = public_path( 'uploads/projects/large/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->scaleDown( 1200 );
                $image->save( $destPath );

                $project->image = $fileName;
                $project->save();

                if ( $oldImage != null ) {
                    File::delete( 'uploads/projects/small/'. $oldImage );
                    File::delete( 'uploads/projects/large/'. $oldImage );
                }
            }
        }

        return response()->json( [
            'status' => true,
            'message' => 'Project updated successfully',
        ] );
    }

    public function show( string $id ) {
        $project = Project::find( $id );

        if ( $project === null ) {
            return response()->json( [
                'status' => false,
                'message' => 'Project not found',
            ] );
        }

        return response()->json( [
            'status' => true,
            'data' => $project,
        ] );
    }

    public function destroy( string $id ) {
        $project = Project::find( $id );

        if ( $project === null ) {
            return response()->json( [
                'status' => false,
                'message' => 'Project not found',
            ] );
        }

        File::delete( 'uploads/projects/small/'. $project->image );
        File::delete( 'uploads/projects/large/'. $project->image );

        $project->delete();

        return response()->json( [
            'status' => true,
            'message' => 'Project deleted successfully',
        ] );
    }
}
