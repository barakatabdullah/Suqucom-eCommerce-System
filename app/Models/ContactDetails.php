<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactDetails extends Model
{
    use HasFactory;

    protected $fillable = ['city', 'phone'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
