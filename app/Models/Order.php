<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory,SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['user_id','total_amount','status','payment_type','payment_id'];

    protected static function booted()
    {
        static::creating(function ($order) {
            if(empty($order->id)){
                $order->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
