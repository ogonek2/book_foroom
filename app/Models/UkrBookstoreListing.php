<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkrBookstoreListing extends Model
{
    protected $table = 'ukr_bookstore_listings';

    protected $fillable = [
        'store_key',
        'external_id',
        'title',
        'url',
        'price',
        'image_url',
        'author',
        'search_query',
    ];
}
