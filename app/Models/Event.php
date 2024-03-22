<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $casts = [
        'all_day' => 'boolean',
        'is_recurrent' => 'boolean',
        'recurrence' => 'json',
    ];

    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'ends_at',
        'location',
        'link',
        'recurrence',
        'all_day',
        'is_recurrent',
        'synced_google',
        'google_calendar_id',
        'owner_user_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
