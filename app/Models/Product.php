<?php

namespace App\Models;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Variant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'slug', 'price', 'image', 'description', 'category_id'];

    protected $appends = ['price_after_offer', 'variant_price', 'variant_price_after_offer'];

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
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    /**
     * Scope a query to only include inactive categories.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeInactive($query)
    {
        $query->where('status', 0);
    }

    /**
     * Get the category that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the offer associated with the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function offer(): HasOne
    {
        return $this->hasOne(Offer::class);
    }

    /**
     * Get all of the variants for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    /**
     * Get all of the attributes for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }

    public function getPriceAfterOfferAttribute()
    {
        if ($this->offer && $this->offer->is_active) {
            if ($this->offer->type == Offer::FIXED) {
                if (($this->price - $this->offer->value) > 0) {
                    return $this->price - $this->offer->value;
                } else {
                    return $this->price;
                }
            }
            if ($this->offer->type == Offer::VARIABLE) {
                if ($this->price - (($this->price * $this->offer->value) / 100) > 0) {
                    return $this->price - (($this->price * $this->offer->value) / 100);
                } else {
                    return $this->price;
                }
            }
        }
        return $this->price;
    }

    public function getVariantPriceAttribute()
    {
        if ($this->variants->count() != 0) {
            $min = min($this->variants->pluck('price')->toArray());
            $max = max($this->variants->pluck('price')->toArray());
            if ($min == $max) {
                return $min;
            }
            return '(' . $min . ',' . $max . ')';
        }
        return $this->price;
    }

    public function getVariantPriceAfterOfferAttribute()
    {
        if ($this->variants->count() != 0) {
            $min = min($this->variants->pluck('price_after_offer')->toArray());
            $max = max($this->variants->pluck('price_after_offer')->toArray());
            if ($min == $max) {
                return $min;
            }
            return '(' . $min . ',' . $max . ')';
        }
        return $this->price;
    }

    /**
     * Change Product Status
     *
     * @return void
     */
    public function ScopeChangeStatus()
    {
        return $this->update(['status' => !$this->status]);
    }

}
