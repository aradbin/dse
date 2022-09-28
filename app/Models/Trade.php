<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
