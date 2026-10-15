<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageFaq extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'question',
        'answer',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->orderBy('sort_order')->orderBy('id');
    }
}
