<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function($where) use ($search) {
                $where->where('name', 'like', '%'.$search.'%');
                $where->orWhere('code', 'like', '%'.$search.'%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        })->when($filters['se_index'] ?? null, function ($query, $se_index) {
            $query->where('se_index', $se_index);
        })->when($filters['category'] ?? null, function ($query, $category) {
            $query->where('category', $category);
        })->when($filters['sector'] ?? null, function ($query, $sector) {
            $query->where('sector', $sector);
        });
    }
}
