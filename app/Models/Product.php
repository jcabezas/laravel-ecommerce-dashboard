<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'stock',
        'provider',
        'external_id',
        'price',
    ];

    protected $casts = [
        'stock' => 'integer',
        'price' => 'decimal:2',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
