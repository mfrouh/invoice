<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'url'];

    /**
     * Get the order associated with the Invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }
}
