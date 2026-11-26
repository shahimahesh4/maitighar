<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequest extends Model
{
    protected $fillable = ['product_id', 'full_name', 'email', 'phone', 'city', 'address', 'quantity', 'message', 'status'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
