<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['judul','genre','author','terbit','foto'];

    public function category()
    {
        return $this->belongsTo(category::class);
    }

}
