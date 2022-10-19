<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Broker extends Model
{
    use SoftDeletes;

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }
}
