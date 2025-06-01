<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ArticleAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');

// Вступление в группу
Route::post('/groups/{group}/join', [GroupController::class, 'join'])
    ->name('groups.join')
    ->can('join-group', 'group');

Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
Route::get('/groups/{group}', [GroupController::class, 'show'])
    ->name('groups.show');
Route::delete('/groups/{group}', [GroupController::class, 'destroy'])
    ->name('groups.destroy')
    ->middleware('can:manage-group,group');
Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
Route::delete('articles/{article}', [ArticleController::class, 'destroy'])
    ->name('articles.destroy');
Route::post('/upload-image', [ArticleController::class, 'upload'])
    ->name('image.upload');

Route::get('/articles/{article}/export-docx', [ArticleController::class, 'exportDocx'])->name('articles.export.docx');


Route::post('/articles/search', [ArticleController::class, 'postSearch'])->name('articles.search');
Route::middleware(['role:admin'])->prefix('admin_panel')->group(function () {
    Route::get('/',[HomeController::class, 'index'])->name('homeAdmin');

    Route::resource('users', UserController::class);
    Route::resource('admin.articles', ArticleAdminController::class)->parameters(['articles' => 'admin']);
    Route::get('articles/{article}/edit', [ArticleAdminController::class, 'edit'])->name('admin.articles.edit');
    Route::put('articles/{article}', [ArticleAdminController::class, 'update'])->name('admin.articles.update');
    Route::get('/articles/{article}', [ArticleAdminController::class, 'show'])->name('admin.articles.show');

    Route::post('/articles/search', [ArticleController::class, 'search'])->name('admin.articles.search');
    Route::delete('/articles/{article}', [ArticleAdminController::class, 'destroy'])->name('admin.articles.destroy');
    Route::get('/articles/{article}/pdf', [ArticleAdminController::class, 'exportPdf'])
        ->name('articles.pdf');
    Route::delete('articles/{article}', [ArticleController::class, 'destroy'])
        ->name('admin.articles.destroy');

    Route::get('/get-gost-fields/{standard}', [ArticleAdminController::class, 'getGostFieldsJson']);
    Route::get('/get-components/{standard}', [ArticleAdminController::class, 'getComponentsJson']);
    Route::post('/components', [ComponentController::class, 'store'])->name('components.store');
    Route::get('/components/create', [ComponentController::class, 'create'])->name('components.create');
});

Route::get('/articles/{article}/pdf', [ArticleController::class, 'exportPdf'])
    ->name('articles.pdf');

Route::get('/get-gost-fields/{standard}', [ArticleController::class, 'getGostFieldsJson']);

Route::get('/get-components/{standard}', [ArticleController::class, 'getComponentsJson']);
Route::post('/components', [ComponentController::class, 'store'])->name('components.store');
Route::get('/components/create', [ComponentController::class, 'create'])->name('components.create');
Route::post('/articles/search', [ArticleController::class, 'search'])->name('articles.search');

require __DIR__.'/auth.php';
