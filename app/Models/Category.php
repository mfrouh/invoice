<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'status'];

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->slug = str_replace(' ', '_', $model->name);
        });
    }

    /**
     * Scope a query to only include active categories.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    /**
     * Scope a query to only include inactive categories.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return void
     */
    public function scopeInactive($query)
    {
        $query->where('status', 0);
    }

    /**
     * Get all of the products for the Category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Change Category Status.
     *
     * @return void
     */
    public function ScopeChangeStatus()
    {
        return $this->update(['status' => !$this->status]);
    }
}
