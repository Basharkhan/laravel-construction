<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller {
    // return all active articles

    public function index() {
        $articles = Article::where( 'status', 1 )->orderBy( 'created_at', 'desc' )->get();
        return response()->json( [
            'status' => true,
            'data' => $articles,
        ] );

    }

    // return latest active articles

    public function latestArticles( Request $reuest ) {
        $articles = Article::where( 'status', 1 )
        ->take( $reuest->get( 'limit' ) )
        ->orderBy( 'created_at', 'desc' )->get();
        return response()->json( [
            'status' => true,
            'data' => $articles,
        ] );

    }
}
