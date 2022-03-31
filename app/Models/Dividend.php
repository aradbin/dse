<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dividend extends Model
{
    use HasFactory;

    public function Organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
