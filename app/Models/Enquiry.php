<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = [
        'product_id', 'product_name', 'customer_name', 'customer_phone', 'customer_email',
        'variant', 'message', 'source', 'status',
    ];

    protected $casts = [
        'variant' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
