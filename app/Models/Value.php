<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Value extends Model
{
    use HasFactory;

    protected $table = 'values';

    protected $fillable = ['value', 'attribute_id'];

    /**
     * Get the attribute that owns the Value.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * The variants that belong to the Value.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(Variant::class, 'variant_value', 'value_id', 'variant_id');
    }
}
