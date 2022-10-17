<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portfolio extends Model
{
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function allTransactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function transactions()
    {
        return $this->allTransactions()->where(function($whereGroup){
            $whereGroup->where('type','!=',3) // buy
                ->where('type','!=',4) ; // sell
        });
    }

    public function trades()
    {
        return $this->allTransactions()->where(function($whereGroup){
            $whereGroup->where('type',3) // buy
                ->orWhere('type',4) ; // sell
        });
    }
}
