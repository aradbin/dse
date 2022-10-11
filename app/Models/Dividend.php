<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dividend extends Model
{
    use SoftDeletes;

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
