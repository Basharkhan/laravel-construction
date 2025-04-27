<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller {
    public function latestProjects( Request $request ) {
        $projects = Project::where( 'status', 1 )
        ->take( $request->get( 'limit' ) )
        ->orderBy( 'created_at', 'desc' )
        ->get();

        return response()->json( [
            'status' => true,
            'data' => $projects,
        ] );
    }

    public function allProjects() {
        $projects = Project::where( 'status', 1 )
        ->orderBy( 'created_at', 'desc' )
        ->get();

        return response()->json( [
            'status' => true,
            'data' => $projects,
        ] );
    }

    /**
    * Get project by id
    */

    public function show( int $id ) {
        $project = Project::where( 'status', 1 )
        ->where( 'id', $id )
        ->first();

        if ( !$project ) {
            return response()->json( [
                'status' => false,
                'message' => 'Project not found',
            ], 404 );
        }

        return response()->json( [
            'status' => true,
            'data' => $project,
        ] );
    }
}
