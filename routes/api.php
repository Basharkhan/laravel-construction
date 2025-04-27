<?php

use App\Http\Controllers\admin\ArticleController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\MemberController;
use App\Http\Controllers\admin\ProjectController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\admin\TestimonialController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\front\ServiceController as FrontServiceController;
use App\Http\Controllers\front\ProjectController as FrontProjectController;
use App\Http\Controllers\front\ArticleController as FrontArticleController;
use App\Http\Controllers\front\TestimonialController as FrontTestimonialController;
use App\Http\Controllers\front\MemberController as FrontMemberController;


Route::post('authenticate', [AuthenticationController::class, 'authenticate']);
// services
Route::get('get-services', [FrontServiceController::class, 'index']);
Route::get('get-latest-services', [FrontServiceController::class, 'latestServices']);
Route::get('get-service/{id}', [FrontServiceController::class, 'show']);

// projects
Route::get('get-projects', [FrontProjectController::class, 'allProjects']);
Route::get('get-latest-projects', [FrontProjectController::class, 'latestProjects']);

// articles
Route::get('get-articles', [FrontArticleController::class, 'index']);
Route::get('get-latest-articles', [FrontArticleController::class, 'latestArticles']);
Route::get('get-article/{id}', [FrontArticleController::class, 'show']);

// testimonials
Route::get('get-testimonials', [FrontTestimonialController::class, 'index']);

// members
Route::get('get-members', [FrontMemberController::class, 'index']);

// protected route
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('logout', [AuthenticationController::class, 'logout']);

    // services
    Route::post('services', [ServiceController::class, 'store']);
    Route::put('services/{id}', [ServiceController::class, 'update']);
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{id}', [ServiceController::class, 'show']);
    Route::delete('services/{id}', [ServiceController::class, 'destroy']);

    // projects
    Route::post('projects', [ProjectController::class, 'store']);
    Route::get('projects', [ProjectController::class, 'index']);
    Route::put('projects/{id}', [ProjectController::class, 'update']);
    Route::get('projects/{id}', [ProjectController::class, 'show']);
    Route::delete('projects/{id}', [ProjectController::class, 'destroy']);
    
    // articles
    Route::post('articles', [ArticleController::class, 'store']);
    Route::put('articles/{id}', [ArticleController::class, 'update']);
    Route::get('articles', [ArticleController::class, 'allArticles']);
    Route::get('articles/{id}', [ArticleController::class, 'show']);
    Route::delete('articles/{id}', [ArticleController::class, 'destroy']);

    // testimonials
    Route::post('testimonials', [TestimonialController::class, 'store']);
    Route::put('testimonials/{id}', [TestimonialController::class, 'update']);
    Route::get('testimonials', [TestimonialController::class, 'index']);
    Route::get('testimonials/{id}', [TestimonialController::class, 'show']);
    Route::delete('testimonials/{id}', [TestimonialController::class, 'destroy']);

    // members
    Route::post('members', [MemberController::class, 'store']);
    Route::put('members/{id}', [MemberController::class, 'update']);
    Route::get('members', [MemberController::class, 'index']);
    Route::get('members/{id}', [MemberController::class, 'show']);
    Route::delete('members/{id}', [MemberController::class, 'destroy']);

    // temp image
    Route::post('temp-images', [TempImageController::class, 'store']);
});