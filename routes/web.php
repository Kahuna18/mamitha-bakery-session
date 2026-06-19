<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Admin\ProductController as AdminProduct;
use App\Http\Controllers\Admin\CategoryController as AdminCategory;
use App\Http\Controllers\Admin\CustomerController as AdminCustomer;
use App\Http\Controllers\Admin\SettingController as AdminSetting;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariant;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonial;
use App\Http\Controllers\Admin\ProductReviewController as AdminProductReview;
use App\Http\Controllers\Kitchen\DashboardController as KitchenDashboard;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [ProductController::class, 'index'])->name('menu');
Route::get('/menu/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/cara-pesan', [HomeController::class, 'howToOrder'])->name('how-to-order');
Route::get('/tentang-kami', [HomeController::class, 'about'])->name('about');
Route::get('/kontak', [HomeController::class, 'contact'])->name('contact');

// Order Routes
Route::get('/pesan', [OrderController::class, 'create'])->name('order.create');
Route::post('/pesan', [OrderController::class, 'store'])->name('order.store');
Route::get('/pesan/sukses/{id}', [OrderController::class, 'success'])->name('order.success');
Route::get('/status', [OrderController::class, 'statusForm'])->name('order.status');
Route::post('/status', [OrderController::class, 'checkStatus'])->name('order.check-status');
Route::get('/riwayat', [OrderController::class, 'history'])->middleware('auth')->name('order.history');
Route::get('/profil', [MemberProfileController::class, 'index'])->middleware('auth')->name('member.profile');

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/check-new-orders', [AdminDashboard::class, 'checkNewOrders'])->name('check-new-orders');
    Route::get('/kitchen', [KitchenDashboard::class, 'index'])->name('kitchen');
    Route::get('/reports', [AdminReport::class, 'index'])->name('reports.index');
    Route::get('/reports/export/pdf', [AdminReport::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export/csv', [AdminReport::class, 'exportCsv'])->name('reports.export.csv');

    // Orders
    Route::get('/orders', [AdminOrder::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrder::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [AdminOrder::class, 'updateStatus'])->name('orders.update-status');
    Route::get('/orders/{order}/invoice', [AdminOrder::class, 'invoice'])->name('orders.invoice');
    Route::delete('/orders/{order}', [AdminOrder::class, 'destroy'])->name('orders.destroy');

    // Products
    Route::resource('products', AdminProduct::class)->except('show');
    Route::resource('products.variants', AdminProductVariant::class)->except('show');

    // Categories
    Route::resource('categories', AdminCategory::class)->except('show');

    // Testimonials
    Route::resource('testimonials', AdminTestimonial::class)->except('show');

    // Product Reviews
    Route::resource('reviews', AdminProductReview::class)->except('show');

    // Customers
    Route::get('/customers', [AdminCustomer::class, 'index'])->name('customers');
    Route::get('/customers/{customer}', [AdminCustomer::class, 'show'])->name('customers.show');
    Route::delete('/customers/{customer}', [AdminCustomer::class, 'destroy'])->name('customers.destroy');
    Route::post('/customers/reset', [AdminCustomer::class, 'resetAll'])->name('customers.reset');

    // Settings
    Route::get('/settings', [AdminSetting::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSetting::class, 'update'])->name('settings.update');
});

// Kitchen Routes
Route::middleware(['auth', 'verified', 'role:kitchen,admin'])->prefix('kitchen')->name('kitchen.')->group(function () {
    Route::get('/', [KitchenDashboard::class, 'index'])->name('dashboard');
    Route::get('/check-new-tasks', [KitchenDashboard::class, 'checkNewTasks'])->name('check-new-tasks');
    Route::post('/tasks/{task}/status', [KitchenDashboard::class, 'updateStatus'])->name('task.update-status');
    Route::get('/print/{id}', [KitchenDashboard::class, 'print'])->name('print');
});

require __DIR__ . '/auth.php';
Route::get('/migrate-db-now', function() {
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
    return 'Database successfully migrated!';
});
