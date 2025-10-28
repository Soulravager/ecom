<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserData extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'user_data';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'address',
        'phone_number',
        'country_code',
        'pincode',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
