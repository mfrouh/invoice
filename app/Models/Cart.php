<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'sku', 'product_id', 'name', 'price', 'quantity', 'details','total_price'];

    /**
     * Get the product that owns the OrderDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the customer that owns the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function ScopeClear(Builder $query)
    {
        return $query->where('customer_id', auth()->id())->delete();
    }

    public function ScopeGetContent(Builder $query)
    {
        return $query->where('customer_id', auth()->id())->select(
            ['product_id', 'sku', 'name', 'price', 'quantity', 'details', 'total_price'])->get();
    }

    public function setTotalPriceAttribute($value)
    {
        $this->attributes['total_price'] = $this->quantity * $this->price;
    }

}
