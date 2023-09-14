<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $appends = ['is_liked'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(BlogLike::class);
    }
    public function getImageAttribute($value)
    {
        if ($value != null)
            return "/uploads/images/post/" . $value;
    }

    public function scopeSearch($query, $value)
    {
        if ($value != null)
            return  $query->where('title', 'like', '%' . $value . '%')->orWhere('description', 'like', '%' . $value . '%');
    }

    public function scopeFilterBy($query, $value)
    {
        if ($value != null)
            if ($value == 'latest') {
                return $query->latest();
            }
        if ($value == 'mostliked') {
            return $query->orderBy('likes_count', 'desc');
        }
    }

    public function getIsLikedAttribute()
    {
        $userId = auth('sanctum')->user()->id;
        $like = $this->likes->first(function ($value) use ($userId) {
            return $value->user_id == $userId;
        });

        if ($like) {
            return true;
        }

        return false;
    }
}
