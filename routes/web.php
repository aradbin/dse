<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PortfoliosController;
use App\Http\Controllers\TransactionsController;
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

Route::get('/', [DashboardController::class, 'home'])
    ->name('home');

// Auth

Route::get('register', [RegisterController::class, 'create'])
    ->name('register')
    ->middleware('guest');

Route::post('register', [RegisterController::class, 'store'])
    ->name('register.store')
    ->middleware('guest');

Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store')
    ->middleware('guest');

Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// Dashboard

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

// Watchlist

Route::get('watchlist', [UsersController::class, 'watchlist'])
    ->name('watchlist')
    ->middleware('auth');

// Portfolio

Route::get('portfolio', [PortfoliosController::class, 'index'])
    ->name('portfolio')
    ->middleware('auth');

Route::get('portfolio/all', [PortfoliosController::class, 'all'])
    ->name('portfolio.all')
    ->middleware('auth');

Route::post('portfolio', [PortfoliosController::class, 'store'])
    ->name('portfolio.store')
    ->middleware('auth');

Route::get('portfolio/{id}', [PortfoliosController::class, 'show'])
    ->name('portfolio.show')
    ->middleware('auth');

// Transactions

Route::post('transactions', [TransactionsController::class, 'store'])
    ->name('transactions.store')
    ->middleware('auth');

// Organizations

Route::get('organizations', [OrganizationsController::class, 'index'])
    ->name('organizations');
    // ->middleware('guest');

Route::get('organizations/initial', [OrganizationsController::class, 'initial'])
    ->name('organizations.initial');
    // ->middleware('guest');

Route::get('organizations/all/{status}', [OrganizationsController::class, 'all'])
    ->name('organizations.all');
    // ->middleware('guest');

Route::get('organizations/show/{code}', [OrganizationsController::class, 'show'])
    ->name('organizations.show');
    // ->middleware('guest');

Route::get('organizations/sync/dse', [OrganizationsController::class, 'syncFromDse'])
    ->name('organizations.sync.dse')
    ->middleware('auth');

Route::get('organizations/sync/amarstock', [OrganizationsController::class, 'syncFromAmarStock'])
    ->name('organizations.sync.amarstock')
    ->middleware('auth');

Route::get('organizations/sync/amarstock/dividend', [OrganizationsController::class, 'syncDividend'])
    ->name('organizations.sync.amarstock.dividend')
    ->middleware('auth');

Route::get('organizations/watch/{id}', [OrganizationsController::class, 'watch'])
    ->name('organizations.watch')
    ->middleware('auth');

// Cache

Route::get('/clear', function() {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    $exitCode = Artisan::call('view:clear');
    return 'Cache is cleared';
});

// Users

// Route::get('users', [UsersController::class, 'index'])
//     ->name('users')
//     ->middleware('auth');

// Route::get('users/create', [UsersController::class, 'create'])
//     ->name('users.create')
//     ->middleware('auth');

// Route::post('users', [UsersController::class, 'store'])
//     ->name('users.store')
//     ->middleware('auth');

// Route::get('users/{user}/edit', [UsersController::class, 'edit'])
//     ->name('users.edit')
//     ->middleware('auth');

// Route::put('users/{user}', [UsersController::class, 'update'])
//     ->name('users.update')
//     ->middleware('auth');

// Route::delete('users/{user}', [UsersController::class, 'destroy'])
//     ->name('users.destroy')
//     ->middleware('auth');

// Route::put('users/{user}/restore', [UsersController::class, 'restore'])
//     ->name('users.restore')
//     ->middleware('auth');

// Contacts

// Route::get('contacts', [ContactsController::class, 'index'])
//     ->name('contacts')
//     ->middleware('auth');

// Route::get('contacts/create', [ContactsController::class, 'create'])
//     ->name('contacts.create')
//     ->middleware('auth');

// Route::post('contacts', [ContactsController::class, 'store'])
//     ->name('contacts.store')
//     ->middleware('auth');

// Route::get('contacts/{contact}/edit', [ContactsController::class, 'edit'])
//     ->name('contacts.edit')
//     ->middleware('auth');

// Route::put('contacts/{contact}', [ContactsController::class, 'update'])
//     ->name('contacts.update')
//     ->middleware('auth');

// Route::delete('contacts/{contact}', [ContactsController::class, 'destroy'])
//     ->name('contacts.destroy')
//     ->middleware('auth');

// Route::put('contacts/{contact}/restore', [ContactsController::class, 'restore'])
//     ->name('contacts.restore')
//     ->middleware('auth');

// // Reports

// Route::get('reports', [ReportsController::class, 'index'])
//     ->name('reports')
//     ->middleware('auth');

// // Images

// Route::get('/img/{path}', [ImagesController::class, 'show'])
//     ->where('path', '.*')
//     ->name('image');