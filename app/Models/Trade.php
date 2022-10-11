<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trade extends Model
{
    use SoftDeletes;

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
