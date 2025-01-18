<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model {
    use HasFactory;

    protected $fillable = [ 'user_id', 'items' ];
    // Allow mass assignment
    protected $casts = [
        'items' => 'array', // Automatically cast the JSON column to an array
    ];

    public function user() {
        return $this->belongsTo( User::class );
    }
}
