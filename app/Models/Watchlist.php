<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Watchlist extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('myWatchlist', function (Builder $builder) {
            $builder->where('user_id',Auth::user()->id);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->hasOne(Organization::class);
    }
}
