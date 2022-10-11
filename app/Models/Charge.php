<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Charge extends Model
{
    use SoftDeletes;

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}
