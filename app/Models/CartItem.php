<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
class CartItem extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['`id`','user_id', 'product_id', 'quantity'];

    public $incrementing = false;   
    protected $keyType = 'string';

    protected static function boot()    
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function user()
{
    return $this->belongsTo(User::class);
}

}
