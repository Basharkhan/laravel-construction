<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ArticleController extends Controller {
    public function store( Request $request ) {
        $request->merge( [ 'slug' => Str::slug( $request->slug ) ] );

        $validator = Validator::make( $request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:articles,slug',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status' => false,
                'message' => $validator->errors(),
            ] );
        }

        $article = new Article();
        $article->title = $request->title;
        $article->slug = Str::slug( $request->slug );
        $article->author = $request->author;
        $article->content = $request->content;

        $article->status = $request->status;

        $article->save();

        // save temp image
        $imageId = $request->imageId;
        if ( $imageId > 0 ) {
            $tempImage = TempImage::find( $imageId );

            if ( $tempImage != null ) {
                $extArray = explode( '.', $tempImage->name );
                $ext = last( $extArray );
                $fileName = strtotime( 'now' ).$article->id.'.'.$ext;

                // get image from temp
                $sourcePath = public_path( 'uploads/temp/'. $tempImage->name );

                // create small thumbnail
                $destPath = public_path( 'uploads/articles/small/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->coverDown( 500, 600 );
                $image->save( $destPath );

                // create large thumbnail
                $destPath = public_path( 'uploads/articles/large/'. $fileName );
                $manager = new ImageManager( Driver::class );
                $image = $manager->read( $sourcePath );
                $image->scaleDown( 1200 );
                $image->save( $destPath );

                $article->image = $fileName;
                $article->save();
            }
        }

        return response()->json( [
            'status' => true,
            'message' => 'Article created successfully',
        ] );
    }
}
