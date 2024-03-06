<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ArticleController;
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

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
Route::delete('/articles/{article}', [ArticleController::class, 'delete'])->name('articles.delete');


Route::post('/articles/search', [ArticleController::class, 'postSearch'])->name('articles.search');


Route::middleware(['role:admin'])->prefix('admin_panel')->group(function () {
    Route::get('/',[HomeController::class, 'index'])->name('homeAdmin');

    Route::resource('users', UserController::class);
    Route::resource('category', CategoryController::class );
    Route::resource('admin.articles', ArticleAdminController::class)->parameters(['articles' => 'admin']);
    Route::get('articles/{article}/edit', [ArticleAdminController::class, 'edit'])->name('admin.articles.edit');
    Route::put('articles/{article}', [ArticleAdminController::class, 'update'])->name('admin.articles.update');
//    Route::get('admin_panel/admin/articles/{article}', [ArticleAdminController::class, 'show'])->name('admin.articles.show');
    Route::get('/articles/{article}', [ArticleAdminController::class, 'show'])->name('admin.articles.show');
//
    Route::post('/articles/search', [ArticleAdminController::class, 'postSearch'])->name('admin.articles.search');
//    Route::get('/admin_panel/admin/articles', [ArticleAdminController::class, 'index'])->name('admin.articles.index');
    Route::delete('/articles/{article}', [ArticleAdminController::class, 'destroy'])->name('admin.articles.destroy');
});


require __DIR__.'/auth.php';
