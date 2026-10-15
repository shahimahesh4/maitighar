<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Specifying the database table associated with this model
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'grand_total',
        'payment_method',
        'payment_status',
        'status',
        'confirmation_sent_at',
        'currency',
        'shipping_amount',
        'shipping_method',
        'notes',
        'coupon_code',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'confirmation_sent_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }
}
