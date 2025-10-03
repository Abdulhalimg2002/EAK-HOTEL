<?php


use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;
use App\Http\Controllers\Admin\AdminhomeController;
use App\Http\Controllers\Admin\AHouseController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UCategoryController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\UNotificationController;





Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect('/Adashboard')
            : redirect('/home');
    }

    return view("landingpage.index");
})->name('landingpage.index');



//admin route:


Route::middleware(['auth', AdminMiddleware::class])->group(function() {
    Route::get('/Adashboard', [AdminhomeController::class, 'index'])->name('Adashboard');
    Route::resource('house', AHouseController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('user', AdminUserController::class);
    Route::resource('history', HistoryController::class);

    // الإشعارات
    Route::get('/admin/notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications.index');
Route::post('/admin/notifications/{id}/mark-read', [AdminNotificationController::class, 'markRead'])->name('admin.notifications.markRead');
  Route::resource('category', CategoryController::class)->names([
    'index' => 'admin.category.index',
    'create' => 'admin.category.create',
    'store' => 'admin.category.store',
    'edit' => 'admin.category.edit',
    'update' => 'admin.category.update',
    'destroy' => 'admin.category.destroy',
]);
});

   


    



Route::middleware(['auth', UserMiddleware::class])
->group(function () {
    
    Route::get('/home', [HomeController::class,'index'] )->name('home');
    Route::get('/home/{id}', [HouseController::class, 'show'])->name('home.show');
    Route::get('house/index', [HouseController::class, 'index'])->name('houses.index');
    Route::get('house/create', [HouseController::class, 'create'])->name('houses.create');
    Route::post('/house', [HouseController::class, 'store'])->name('houses.store');
    Route::get('/houses/{id}/edit', [HouseController::class, 'edit'])->name('houses.edit');
    Route::put('/houses/{id}', [HouseController::class, 'update'])->name('houses.update');
    Route::delete('/houses/{id}', [HouseController::class, 'destroy'])->name('houses.destroy');

    Route::get('/Profile', [ProfileController::class,'index'] )->name('Profile');
    Route::get('/Profile/{id}/edit', [ProfileController::class, 'edit'])->name('Profile.edit');
    Route::put('/Profile/{id}', [ProfileController::class, 'update'])->name('Profile.update');

    Route::get('booking/create/{houseId}', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{id}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    
Route::post('/bookings/{id}/approve', [BookingController::class, 'approveFromNotification'])->name('bookings.approveFromNotification');
    // هنا إضافة مسارات الدفع الوهمية
    Route::get('/bookings/payment-preview/{houseId}', [BookingController::class, 'paymentPreview'])->name('bookings.paymentPreview');
    Route::post('/bookings/confirm-payment', [BookingController::class, 'confirmPayment'])->name('bookings.confirmPayment');
    Route::get('/notifications', [UNotificationController::class, 'index'])->name('Unotifications.index');
   Route::put('/notifications/{id}/read', [UNotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

});

 

















