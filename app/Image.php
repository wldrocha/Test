<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['path','product_id'];

    public function product () 
    {
        $this->belongTo(Product::class);
    }
}
