<?php

use App\Http\Controllers\DocumentCollectionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TemporalityController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthenticateUser;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('login');
});

Auth::routes(['register' => false]);

Route::get('/home', function(){
    return redirect('dashboard');
});

Route::middleware(AuthenticateUser::class)->group(function () {
    Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('temporalitys', [HomeController::class, 'temporalitys'])->name('temporalitys');
    Route::get('documents/{project_id}', [HomeController::class, 'documents'])->name('documents');
    Route::get('document-collections', [HomeController::class, 'collections'])->name('document.collections');
    Route::get('users', [HomeController::class, 'users'])->name('users');
    Route::get('logs', [HomeController::class, 'logs'])->name('logs');
    Route::get('alerts', [HomeController::class, 'alerts'])->name('alerts');
    Route::get('label/{id}', [HomeController::class, 'label'])->name('label');
    Route::get('loan_form/{id}', [HomeController::class, 'loan_form'])->name('loan_form');
    Route::get('reports', [HomeController::class, 'reports'])->name('reports');
    Route::get('box', [HomeController::class, 'box'])->name('box');
    
    Route::get('/documents/{id}/files', [DocumentController::class, 'getFiles']);
    Route::get('/documents/{id}/download-all', [DocumentController::class, 'downloadAllFiles'])->name('download.all.files');
    
    Route::get('/user/{id}/projects', [UserController::class, 'user_projects'])->name('user_projects');;
    Route::post('/user/assing-projects', [UserController::class, 'assing_projects'])->name('assing.projects');
    Route::get('/user/assing/delete', [UserController::class, 'assing_delete'])->name('assing.delete');
    
    Route::get('/temporality/{id}/volatile_columns', [TemporalityController::class, 'getVolatileColumns']);
    
    
    Route::post('create-project', [ProjectController::class, 'create'])->name('create.project');
    Route::post('create-document', [DocumentController::class, 'create'])->name('create.document');
    Route::post('create-temporality', [TemporalityController::class, 'create'])->name('create.temporality');
    Route::post('create-document-collection', [DocumentCollectionController::class, 'create'])->name('create.loan'); // Controlador corrigido
    Route::post('create-user', [UserController::class, 'create'])->name('create.user'); // Controlador corrigido
    
    Route::get('delete-collection/{id}', [DocumentCollectionController::class, 'delete'])->name('delete.loan');
    Route::get('delete-document/{id}', [DocumentController::class, 'delete'])->name('delete.document');
    Route::get('delete-temporality/{id}', [TemporalityController::class, 'delete'])->name('delete.temporality');
    Route::get('delete-project/{id}', [ProjectController::class, 'delete'])->name('delete.project');
    Route::get('delete-user/{id}', [UserController::class, 'delete'])->name('delete.user'); // Controlador corrigido

    Route::get('/generate-documents-report', [ReportController::class, 'generateDocumentsReport'])->name('generate.documents.report');
    Route::get('/generate-loans-report', [ReportController::class, 'generateLoansReport'])->name('generate.loans.report');
    
});

Route::get('/show/document/{document_id}', [HomeController::class, 'show_document'])->name('show.document');
Route::get('/show/document_collection/{id}', [HomeController::class, 'document_collection'])->name('show.document_collection');
Route::get('/show/box/{box}', [HomeController::class, 'box'])->name('show.box');

