<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class TempImageController extends Controller {
    public function store( Request $request ) {

        $validator = Validator::make( $request->all(), [
            'image' => 'required|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status' => false,
                'errors' => $validator->errors()
            ] );
        }

        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $imageName = strtotime( 'now' ) .'.'. $ext;

        $model = new TempImage();
        $model->name = $imageName;
        $model->save();

        $image->move( public_path( 'uploads/temp' ), $imageName );

        // create thumbnail
        $sourcePath = public_path( 'uploads/temp/'. $imageName );
        $destpath = public_path( 'uploads/temp/thumb/'. $imageName );

        $manager = new ImageManager( Driver::class );
        $image = $manager->read( $sourcePath );

        $image->coverDown( 300, 300 );
        $image->save( $destpath );

        return response()->json( [
            'status' => true,
            'message' => 'Image added successfully',
            'data' => $model
        ] );
    }
}
