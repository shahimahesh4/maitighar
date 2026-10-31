<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\ContactMessage;
use App\Models\Coupon;
use App\Models\InventoryLog;
use App\Models\MediaAsset;
use App\Models\MediaFolder;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingRate;
use App\Models\Subscriber;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class OperationalDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@maitighar.test'], [
            'name' => 'Demo Administrator', 'phone' => '9800000001', 'password' => 'password',
            'email_verified_at' => now(), 'role' => 'super_admin', 'job_title' => 'Academy Administrator',
            'department' => 'Administration', 'employee_id' => 'MG-ADM-001', 'joining_date' => '2024-01-15',
            'employment_type' => 'Full time', 'city' => 'Bhairahawa', 'state' => 'Lumbini Province',
        ]);

        $trainer = User::updateOrCreate(['email' => 'trainer@maitighar.test'], [
            'name' => 'Anisha Shrestha', 'phone' => '9800000002', 'password' => 'password',
            'email_verified_at' => now(), 'role' => 'mentor', 'job_title' => 'Senior Heritage Mentor',
            'department' => 'Heritage Experiences', 'employee_id' => 'MG-TRN-001', 'joining_date' => '2024-03-01',
            'employment_type' => 'Full time', 'admin_permissions' => ['services', 'content'],
            'city' => 'Bhairahawa', 'state' => 'Lumbini Province',
        ]);

        $customer = User::updateOrCreate(['email' => 'customer@example.com'], [
            'name' => 'Demo Customer', 'phone' => '9800000003', 'password' => 'password',
            'email_verified_at' => now(), 'role' => 'customer', 'address' => 'Siddharthanagar-8',
            'city' => 'Bhairahawa', 'state' => 'Lumbini Province', 'postal_code' => '32900',
        ]);

        $student = User::updateOrCreate(['email' => 'student@maitighar.test'], [
            'name' => 'Sanjana Thapa', 'phone' => '9800000004', 'password' => 'password',
            'email_verified_at' => now(), 'role' => 'student', 'trainer_id' => $trainer->id,
            'date_of_birth' => '2002-05-18', 'gender' => 'female', 'address' => 'Devkota Chowk',
            'city' => 'Bhairahawa', 'state' => 'Lumbini Province', 'guardian_name' => 'Maya Thapa',
            'guardian_phone' => '9800000094', 'education_level' => 'Higher Secondary',
        ]);

        
        foreach ([
            ['WELCOME10', 'percent', 10, 50, now()->addMonths(6)],
            ['SAVE15', 'fixed', 15, 60, now()->addMonths(3)],
            ['STUDENT15', 'percent', 15, 40, now()->addYear()],
        ] as [$code, $type, $value, $minimum, $expiry]) {
            Coupon::updateOrCreate(['code' => $code], ['type' => $type, 'value' => $value, 'min_amount' => $minimum, 'is_active' => true, 'expires_at' => $expiry]);
        }

        foreach ([
            ['Standard Delivery', 'Sydney Metro (NSW)', 9.95, 1, 2],
            ['Standard Delivery', 'Regional NSW & ACT', 14.50, 2, 4],
            ['Express Delivery', 'Interstate (VIC, QLD, SA, WA)', 19.95, 2, 3],
            ['Free Local Pickup', 'Auburn Store / Studio', 0, 1, 1],
        ] as [$method, $zone, $cost, $min, $max]) {
            ShippingRate::updateOrCreate(['shipping_method' => $method, 'zone_name' => $zone], ['cost' => $cost, 'min_delivery_days' => $min, 'max_delivery_days' => $max, 'is_active' => true]);
        }

        foreach (['beautylover@example.com', 'studentnews@example.com', 'offers@example.com'] as $email) {
            Subscriber::updateOrCreate(['email' => $email], ['is_active' => true]);
        }

        $products = Product::where('is_active', true)->orderBy('id')->take(5)->get();
        foreach ($products->take(3) as $product) {
            Wishlist::firstOrCreate(['user_id' => $customer->id, 'product_id' => $product->id]);
            InventoryLog::updateOrCreate(['product_id' => $product->id, 'reason' => 'Demo opening stock'], ['user_id' => $admin->id, 'quantity_change' => 25]);
        }

        $orders = [
            ['DEMO-ORDER-001', 'delivered', 'paid', 'cod', 9.95, 0, now()->subDays(18)],
            ['DEMO-ORDER-002', 'processing', 'paid', 'cod', 14.50, 15.00, now()->subDays(4)],
            ['DEMO-ORDER-003', 'new', 'pending', 'bank_transfer', 9.95, 0, now()->subDay()],
        ];

        foreach ($orders as $orderIndex => [$marker, $status, $paymentStatus, $paymentMethod, $shipping, $discount, $createdAt]) {
            $selected = $products->slice($orderIndex, 2)->values();
            if ($selected->isEmpty()) {
                continue;
            }
            $subtotal = $selected->sum(fn (Product $product) => (float) $product->price);
            $order = Order::updateOrCreate(['user_id' => $customer->id, 'notes' => $marker], [
                'grand_total' => $subtotal + $shipping - $discount, 'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus, 'status' => $status, 'currency' => 'AUD',
                'shipping_amount' => $shipping, 'shipping_method' => $shipping == 9.95 ? 'Standard delivery' : 'Regional delivery',
                'coupon_code' => $discount ? 'SAVE15' : null, 'discount_amount' => $discount, 'created_at' => $createdAt,
            ]);
            foreach ($selected as $product) {
                $order->items()->updateOrCreate(['product_id' => $product->id], ['quantity' => 1, 'unit_amount' => $product->price, 'total_amount' => $product->price]);
            }
            Address::updateOrCreate(['order_id' => $order->id], ['first_name' => 'Demo', 'last_name' => 'Customer', 'phone' => '9800000003', 'street_address' => 'Siddharthanagar-8, near Buddha Chowk', 'city' => 'Bhairahawa', 'state' => 'Lumbini Province', 'zip_code' => '32900']);
        }

        


        foreach ([
            ['Riya Karki', 'riya.demo@example.com', '9800000006', 'Course fee and schedule', 'Please share the next diploma makeup course dates and installment options.', 'new', null],
            ['Nisha Rana', 'nisha.demo@example.com', '9800000007', 'Bridal booking availability', 'Is the bridal makeup team available for my ceremony next month?', 'read', now()->subHours(3)],
        ] as [$name, $email, $phone, $subject, $message, $status, $readAt]) {
            ContactMessage::updateOrCreate(['email' => $email, 'subject' => $subject], ['full_name' => $name, 'phone' => $phone, 'address' => 'Bhairahawa, Nepal', 'message' => $message, 'status' => $status, 'read_at' => $readAt]);
        }

        SupportTicket::updateOrCreate(['user_id' => $customer->id, 'subject' => 'Order delivery update'], ['message' => 'Could you confirm when my processing order will be dispatched?', 'status' => 'in_progress', 'priority' => 'medium', 'admin_reply' => 'Your order is being packed and is expected to dispatch tomorrow.']);

        $academyPage = Page::where('slug', 'academy-training')->first();
        if ($academyPage) {
            foreach ([
                ['How long are academy courses?', 'Course duration varies by program. Contact our team for the current schedule and recommended learning path.'],
                ['Are tools provided for learning sessions?', 'Core tools are available for guided academy practice. Your mentor will explain any personal kit requirements before the session begins.'],
                ['Can beginners apply?', 'Yes. Several academy programs are designed specifically for beginners with no previous professional experience.'],
            ] as $index => [$question, $answer]) {
                $academyPage->faqs()->updateOrCreate(['question' => $question], ['answer' => '<p>'.$answer.'</p>', 'status' => 'published', 'sort_order' => $index + 1]);
            }
        }

        Setting::set('store_name', 'Maitighar');
        Setting::set('store_email', 'maitigharaustralia@gmail.com');
        Setting::set('store_phone', '+61 432 685 546');
        Setting::set('store_address', '2/75 Dartbrook Road, Auburn NSW, Sydney, NSW 2144');
        Setting::set('facebook_url', 'https://www.facebook.com/maitigharaustralia');

        $folder = MediaFolder::firstOrCreate(['parent_id' => null, 'name' => 'Demo Website Images']);
        $source = public_path('images/kba/about-us-academy.png');
        $mediaPath = 'media/demo/about-us-academy.png';
        if (is_file($source) && ! Storage::disk('public')->exists($mediaPath)) {
            Storage::disk('public')->put($mediaPath, file_get_contents($source));
        }
        if (Storage::disk('public')->exists($mediaPath)) {
            MediaAsset::updateOrCreate(['disk' => 'public', 'path' => $mediaPath], ['media_folder_id' => $folder->id, 'name' => 'About Us', 'mime_type' => 'image/png', 'size' => Storage::disk('public')->size($mediaPath), 'alt_text' => 'Maitighar Nepalese Heritage with Love', 'caption' => 'Maitighar - Nepalese Heritage with Love.']);
        }
    }
}
