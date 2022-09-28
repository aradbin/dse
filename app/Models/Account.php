<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }
}
