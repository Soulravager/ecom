<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image'
    ];

    
    protected $keyType = 'string';  
    public $incrementing = false;       
    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->id)) {                
                $product->id = (string) Str::uuid();         }
     });
    }
}
