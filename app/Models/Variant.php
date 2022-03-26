<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Variant extends Model
{
    use HasFactory;

    protected $table = 'variants';

    protected $fillable = ['sku', 'price', 'quantity', 'product_id'];

    protected $appends = ['price_after_offer'];

    /**
     * The values that belong to the Variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function values(): BelongsToMany
    {
        return $this->belongsToMany(Value::class, 'variant_value', 'variant_id', 'value_id');
    }

    /**
     * Get the product that owns the Variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getPriceAfterOfferAttribute()
    {
        $product = Product::WhereHas('offer')->where('id', $this->product_id)->first();
        if ($product && $product->offer->is_active) {
            if ($product->offer->type == Offer::FIXED) {
                if (($this->price - $product->offer->value) > 0) {
                    return $this->price - $product->offer->value;
                } else {
                    return $this->price;
                }
            }
            if ($product->offer->type == Offer::VARIABLE) {
                return $this->price - (($this->price * $product->offer->value) / 100);
            }
        }

        return $this->price;
    }
}
