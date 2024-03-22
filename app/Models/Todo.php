<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;
    protected $table = 'todos';

    protected $fillable = [
        'task',
        'status',
    ];

    public function scopeByOwner(Builder $query, string $type)
    {
        return $query->where('owner_user_id', $type);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
