<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LibraryController;
use App\Models\Book;
use App\Models\Library;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $books = Book::with("author")->paginate(5);
    //dd($books);
    return view('user.index',compact("books"));
});

Route::get('/book', function () {
    $books = Book::with("author")->paginate(10);
    //dd($books);
    return view('user.book',compact("books"));
});

Route::get('/detail/{id}', function ($id) {
    $book = Book::with(["author","category",'libraries'])->find($id);
    // $library = Library::with('name');
    // dd($books);
    return view('user.detail',compact("book"));
})->name("detail");
// Route::view('/detail', 'user.detail');
Route::view('/about', 'user.about');

Route::get('/dashboard',[ DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::name("admin.")->prefix("admin")->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'adminHome'])->name('home');

    //Library
    Route::name("library.")->prefix('library')->group(function () {
        Route::get('/', [LibraryController::class, 'index'])->name('index');
        Route::get('/create', [LibraryController::class, 'create'])->name('create');
        Route::post('/create', [LibraryController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [LibraryController::class, 'destroy'])->name('destroy');
        Route::get('/edit/{id}', [LibraryController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [LibraryController::class, 'update'])->name('update');
    });


        Route::resource('category', CategoryController::class);   //Category

        Route::resource('author', AuthorController::class);  //Author

        Route::resource('book', BookController::class);  //Book

});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::name("user.")->prefix("user")->middleware(['auth', 'is_user'])->group(function () {

    // Route::view('/', 'user.index');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});
