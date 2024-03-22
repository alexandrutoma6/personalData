<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $table = 'documents';

    protected $fillable = [
        'title',
        'description',
        'file',
        'owner_user_id',
    ];
    
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
