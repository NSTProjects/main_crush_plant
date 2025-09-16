<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SalesInvoiceItemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CustomerLedgerController;
use App\Http\Controllers\SyncController;
use App\Models\User;
// use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    // User::create([
    //     'name' => 'Admin User',
    //     'email' => 'admin@gmail.com',
    //     'password' => Hash::make('123456'),
    //     'role' => 'admin',
    // ]);
    return view('auth.login');
});

// ✅ Single dashboard route for all authenticated users
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
// routes/web.php
Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
// ✅ Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('customer', CustomerController::class);
Route::resource('product', ProductController::class);
Route::resource('delivery', DeliveryController::class);
Route::resource('expense', ExpenseController::class);
Route::resource('sales-invoice', SalesInvoiceController::class);

Route::resource('sales-invoice-item', SalesInvoiceItemController::class);
Route::post('sales-invoice-item/filter', [SalesInvoiceItemController::class, 'filter'])->name('seles-item.filter');

// Route::post('test-ajax', function (Request $request) {
//     return response()->json(['message' => 'AJAX working']);
// });



// it is used for destroy because I use a tag
Route::get('customer/delete/{id}', [CustomerController::class, 'destroy'])->name('customer.delete');
Route::get('product/delete/{id}', [ProductController::class, 'destroy'])->name('product.delete');
Route::get('delivery/delete/{id}', [DeliveryController::class, 'destroy'])->name('delivery.delete');
Route::get('expense/delete/{id}', [ExpenseController::class, 'destroy'])->name('expense.delete');
Route::get('salesInvoice/delete/{id}', [SalesInvoiceController::class, 'destroy'])->name('salesInvoice.delete');
Route::get('/sales-invoice/print/{id}', [SalesInvoiceController::class, 'print'])->name('sales-invoice.print');
// It is commming 
Route::get('customer-ledger/{id}', [CustomerController::class, 'showLedger'])->name('customer-ledger');
Route::post('customer-ledger/filter/{id}', [CustomerController::class, 'filterLedger'])->name('customer-ledger.filter');

// Route::post('customer-ledger/filter/{id}', [CustomerController::class, 'filterLedger'])->name('customer-ledger.filter');
Route::post('/customer/storeLedger', [CustomerController::class, 'storeLedger'])->name('customer.storeLedger');
Route::put('/customer/updateLedger/{ledger}', [CustomerController::class, 'updateLedger'])->name('customer.updateLedger');
Route::get('customer/deleteLedger/{id}', [CustomerController::class, 'destroyLedger'])->name('customer.deleteLedger');
Route::get('customer-debit', [CustomerController::class, 'showDebit'])->name('customer-debit');
Route::get('customer-credit', [CustomerController::class, 'showCredit'])->name('customer-credit');

Route::get('journal', [UserController::class, 'journal'])->name('journal');


// forUpdate 
Route::put('/sales-invoice/{sales_invoice}', [SalesInvoiceController::class, 'update'])->name('sales-invoice.update');

// routes/web.php


Route::post('/backup-database', [BackupController::class, 'backupDatabase'])->name('backup.database');
Route::post('/sync', [SyncController::class, 'sync']);
require __DIR__ . '/auth.php';






















// use App\Http\Controllers\AdminController;
// use App\Http\Controllers\ProfileController;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\UserController;


// Route::get('/', function () {
//     return view('auth.login');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::get('/admin', function () {
//         return view('dashboard');
//     });
// });

// Route::middleware(['auth', 'role:user'])->group(function () {
//     Route::get('/user', function () {
//         return view('dashboard');
//     });
// });


// Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard')->middleware('auth');
// Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard')->middleware('auth');

// Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware('auth');
// Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard')->middleware('auth');


// require __DIR__.'/auth.php';
