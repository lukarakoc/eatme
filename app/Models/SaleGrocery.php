<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleGrocery extends Model
{
    protected $guarded = [];

    public function grocery(): BelongsTo
    {
        return $this->belongsTo(Grocery::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
