<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aplikasi extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'demo_url',
        'image',
        'tech_stack',
        'order',
    ];
}
