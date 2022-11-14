<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioOrganization extends Model
{
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
