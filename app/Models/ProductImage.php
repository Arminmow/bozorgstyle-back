<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model {

    use HasFactory;

    protected $table = 'product_images';
    // Explicitly set the table name

    // If you want to allow mass assignment for image_path and product_id
    protected $fillable = [ 'product_id', 'image_path' ];

    public function product() {
        return $this->belongsTo( Product::class );
    }
}
