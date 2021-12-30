<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    use HasFactory;

    const FIXED = 'FIXED';
    const VARIABLE = 'VARIABLE';

    protected $table = 'offers';

    protected $fillable = ['product_id', 'type', 'value', 'message', 'start', 'end'];

    protected $dates = ['start', 'end'];

    protected $appends = ['is_active', 'active_status', 'type_offer', 'start_offer', 'end_offer'];

    /**
     * Get the product that owns the Offer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getTypeOfferAttribute()
    {
        return $this->type == oFFER::FIXED ? __('Fixed') : __('Variable');
    }

    public function getIsActiveAttribute()
    {
        return $this->start <= now() && $this->end >= now() ? 1 : 0;
    }

    public function getActiveStatusAttribute()
    {
        return $this->is_active == 1 ? __('Available') : __('Not Available');
    }

    public function ScopeActive($query)
    {
        $query->where('start', '<=', now())->where('end', '>=', now());
    }

    public function ScopeInactive($query)
    {
        $query->OrWhere('start', '>', now())->OrWhere('end', '<', now());
    }

    public function getEndOfferAttribute()
    {
        return $this->end->format('Y-m-d\TH:i');
    }

    public function getStartOfferAttribute()
    {
        return $this->start->format('Y-m-d\TH:i');
    }
}
