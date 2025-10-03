<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\House;  // التسمية الصحيحة للنموذج
use App\Models\Category;
use App\Models\Booking;
use App\Notifications\HouseCreated;
use App\Notifications\BookingCreated;
use App\Notifications\BookingApproved;
use App\Models\User;

class HouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
{
    $houses = House::with('category', 'booking')->get();

    // تأكد من تسجيل دخول المستخدم
    $UnreadNotificationsCount = auth()->check() ? auth()->user()->unreadNotifications->count() : 0;

    return view('houses.index', compact('houses', 'UnreadNotificationsCount'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('houses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من البيانات المرسلة
        $validated = $request->validate([
            'img.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'location' => 'required|string|max:255',
            'dec_' => 'required|string|max:255',
            'price' => 'required|numeric',
            'numofR' => 'required|integer',
            'City' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'users_id' => 'required|exists:users,id',
            'status' => 'nullable|string|in:pending,approved,rejected',
            'is_featured' => 'nullable|boolean',
        ]);

        // رفع الصور وتخزين المسارات
        $imagePaths = [];

        if ($request->hasFile('img')) {
            foreach ($request->file('img') as $image) {
                if ($image->isValid()) {
                    $imagePaths[] = $image->store('houses', 'public');
                }
            }
        }

        // إنشاء السجل في قاعدة البيانات
$house=        House::create([
            'img' => $imagePaths , // Laravel سيحولها إلى JSON تلقائيًا
            'location' => $validated['location'],
            'dec_' => $validated['dec_'],
            'price' => $validated['price'],
            'numofR' => $validated['numofR'],
            'City' => $validated['City'],
            'status' => $validated['status'] ?? 'pending',
            'category_id' => $validated['category_id'],
            'users_id' => $validated['users_id'],
            'is_featured' => $request->has('is_featured'),
        ]);
      $admin = User::where('role', 'admin')->first();
if ($admin) {
    $admin->notify(new HouseCreated($house));
}

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('home')
                         ->with('success', 'House and images uploaded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       
        $house = House::with('User')->findOrFail($id); // Also eager load user to avoid N+1
    return view('home.show', compact('house'));
       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $house = House::findOrFail($id);
        $categories = Category::all();
        $bookings = Booking::all();

        return view('houses.edit', compact('house', 'categories', 'bookings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $house = House::findOrFail($id);

        // التحقق من البيانات المرسلة
        $validated = $request->validate([
            'img.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'location'=> 'required|string|max:255',
            'dec_' => 'required|string|max:255',
            'price' => 'required|numeric',
            'numofR' => 'required|integer',
            'City'=> 'required|string|max:255',
            'category_id'=>'required|exists:categories,id',
            'users_id'=>'required|exists:users,id',
        ]);
    
        $imagePaths = $house->img ?? [];
    
        // حذف الصور عند تحديث السجل
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $removeImage) {
                if (($key = array_search($removeImage, $imagePaths)) !== false) {
                    Storage::disk('public')->delete($removeImage);
                    unset($imagePaths[$key]);
                }
            }
        }

        // رفع صور جديدة إذا كانت موجودة
        if ($request->hasFile('img')) {
            foreach ($request->file('img') as $image) {
                if ($image->isValid()) {
                    $imagePaths[] = $image->store('houses', 'public');
                }
            }
        }
    
        // تحديث السجل في قاعدة البيانات
        $house->update([
            'img' => array_values($imagePaths), // إعادة ترتيب الأرقام بعد الحذف
            'location' => $validated['location'],
            'dec_' => $validated['dec_'],
            'price' => $validated['price'],
            'numofR' => $validated['numofR'],
            'City' => $validated['City'],
            'category_id' => $validated['category_id'],
            'users_id' => $validated['users_id'],
            'is_featured' => $request->has('is_featured'),
        ]);
    
        return redirect()->route('Profile')->with('success', 'House updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $house = House::findOrFail($id);
    
        if ($house->img) {
            foreach ($house->img as $image) {
                Storage::disk('public')->delete($image);
            }
        }
    
        $house->delete();
        return redirect()->route('Profile')->with('success', 'House deleted successfully!');
    }
}
