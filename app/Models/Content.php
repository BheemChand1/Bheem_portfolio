<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['published' => 'boolean', 'featured' => 'boolean', 'data' => 'array'];
    }

    public function scopePublished($query)
    {
        return $query->where('published', true)->orderBy('sort_order')->orderBy('id');
    }
}
