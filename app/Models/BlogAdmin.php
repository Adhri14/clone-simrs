<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogAdmin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'posts';
    protected $fillable = ['title', 'category_id', 'slug', 'status', 'excerpt', 'body', 'published_at'];

    public function Category()
    {
        return $this->belongsTo(CategoryAdmin::class, 'category_id');
    }
}
