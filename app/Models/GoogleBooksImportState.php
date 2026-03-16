<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleBooksImportState extends Model
{
    protected $fillable = [
        'query',
        'last_start_index',
        'last_total_items',
        'last_run_at',
    ];
}

