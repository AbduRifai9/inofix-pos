<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = ['name', 'code', 'price', 'stock', 'image'];

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Getter untuk URL gambar produk
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // Jika gambar disimpan di storage/public
            if (Storage::disk('public')->exists($this->image)) {
                return Storage::url($this->image);
            } else {
                // Jika bukan path relatif, mungkin ini URL absolut
                return $this->image;
            }
        }
        
        // Gambar default jika tidak ada gambar
        return asset('images/default-product.jpg'); // Atau path gambar default
    }
}
