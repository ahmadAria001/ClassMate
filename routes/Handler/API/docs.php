<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DocRequestController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\DocumentationController;
use App\Http\Middleware\Resources\Docs\CreateActivity;
use App\Http\Middleware\Resources\Docs\CreateComplaint;
use App\Http\Middleware\Resources\Docs\CreateDocumentation;
use App\Http\Middleware\Resources\Docs\Get;
use App\Http\Middleware\Resources\Docs\GetComplaint;
use App\Http\Middleware\Resources\Docs\GetRequest;
use App\Http\Middleware\Resources\Docs\CreateRequest;
use App\Http\Middleware\Resources\Docs\DeleteActivity;
use App\Http\Middleware\Resources\Docs\DeleteComplaint;
use App\Http\Middleware\Resources\Docs\DeleteDocumentation;
use App\Http\Middleware\Resources\Docs\DeleteRequest;
use App\Http\Middleware\Resources\Docs\GetActivity;
use App\Http\Middleware\Resources\Docs\GetDocumentation;
use App\Http\Middleware\Resources\Docs\UpdateActivity;
use App\Http\Middleware\Resources\Docs\UpdateComplaint;
use App\Http\Middleware\Resources\Docs\UpdateDocumentation;
use App\Http\Middleware\Resources\Docs\UpdateRequest;
use Illuminate\Support\Facades\Route;


Route::prefix('/docs')->group(function(){
    Route::get('/', [DocsController::class, 'get'])->middleware(Get::class);
    Route::get('/{filter}', [DocsController::class, 'get'])->middleware(Get::class);
});

Route::prefix('/doc-request')->group(function(){
    Route::get('/',[DocRequestController::class, 'get'])->middleware(GetRequest::class);
    Route::get('/{filter}',[DocRequestController::class, 'get'])->middleware(GetRequest::class);
    Route::post('/',[DocRequestController::class, 'create'])->middleware(CreateRequest::class);
    Route::put('/',[DocRequestController::class, 'edit'])->middleware(UpdateRequest::class);
    Route::delete('/',[DocRequestController::class, 'destroy'])->middleware(DeleteRequest::class);
});

Route::prefix('/complaint')->group(function(){
    Route::get('/', [ComplaintController::class, 'get'])->middleware(GetComplaint::class);
    Route::get('/{filter}', [ComplaintController::class, 'get'])->middleware(GetComplaint::class);
    Route::post('/',[ComplaintController::class, 'create'])->middleware(CreateComplaint::class);
    Route::put('/',[ComplaintController::class, 'edit'])->middleware(UpdateComplaint::class);
    Route::delete('/',[ComplaintController::class, 'destroy'])->middleware(DeleteComplaint::class);
});

Route::prefix('/documentation')->group(function(){
    Route::get('/', [DocumentationController::class, 'get'])->middleware(GetDocumentation::class);
    Route::get('/{filter}', [DocumentationController::class, 'get'])->middleware(GetDocumentation::class);
    Route::post('/',[DocumentationController::class, 'create'])->middleware(CreateDocumentation::class);
    Route::put('/',[DocumentationController::class, 'edit'])->middleware(UpdateDocumentation::class);
    Route::delete('/',[DocumentationController::class, 'destroy'])->middleware(DeleteDocumentation::class);
});

Route::prefix('/activity')->group(function(){
    Route::get('/', [ActivityController::class, 'get'])->middleware(GetActivity::class);
    Route::get('/{filter}', [ActivityController::class, 'get'])->middleware(GetActivity::class);
    Route::post('/',[ActivityController::class, 'create'])->middleware(CreateActivity::class);
    Route::put('/',[ActivityController::class, 'edit'])->middleware(UpdateActivity::class);
    Route::delete('/',[ActivityController::class, 'destroy'])->middleware(DeleteActivity::class);
});