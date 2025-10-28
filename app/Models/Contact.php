<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'email', 'message'];

    protected static function boot()
    {
        parent::boot();
                
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public $incrementing = false;
    protected $keyType = 'string';
}
