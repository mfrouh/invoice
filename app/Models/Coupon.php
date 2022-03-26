<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    use HasFactory;

    const FIXED = 'FIXED';
    const VARIABLE = 'VARIABLE';

    const LESS = 'LESS';
    const MORE = 'MORE';

    protected $fillable = ['code', 'start', 'end', 'condition', 'condition_value', 'type', 'value', 'message', 'times'];

    protected $appends = ['is_active', 'active_status', 'type_coupon', 'condition_coupon', 'end_coupon', 'start_coupon'];

    protected $dates = ['start', 'end'];

    public function getTypeCouponAttribute()
    {
        return $this->type == Offer::FIXED ? __('Fixed') : __('Variable');
    }

    public function getConditionCouponAttribute()
    {
        if ($this->condition) {
            return $this->condition == self::MORE ? __('More') : __('Less');
        }

        return '';
    }

    public function getIsActiveAttribute()
    {
        return $this->start <= now() && $this->end >= now() ? 1 : 0;
    }

    public function getActiveStatusAttribute()
    {
        return $this->is_active == 1 ? __('Available') : __('Not Available');
    }

    public function getEndCouponAttribute()
    {
        return $this->end->format('Y-m-d\TH:i');
    }

    public function getStartCouponAttribute()
    {
        return $this->start->format('Y-m-d\TH:i');
    }

    public function ScopeActive($query)
    {
        $query->where('start', '<=', now())->where('end', '>=', now());
    }

    public function ScopeInactive($query)
    {
        $query->OrWhere('start', '>', now())->OrWhere('end', '<', now());
    }

    /**
     * The customers that belong to the Coupon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'customer_coupon', 'coupon_id', 'customer_id');
    }
}
