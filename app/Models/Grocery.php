<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grocery extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function isProduct() : Attribute {
        return Attribute::make(
            get: fn (string $value) => $value == 1
        );
    }

    public function purchases(): BelongsToMany
    {
        return $this->belongsToMany(Purchase::class, 'purchase_groceries', 'grocery_id');
    }

    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseGrocery::class);
    }

    public static function searchGroceries($keyword): LengthAwarePaginator
    {
        return self::query()
            ->whereRaw('LOWER(name) LIKE (?)', ['%' . strtolower($keyword) . '%'])
            ->orWhereRaw('LOWER(unit_price) LIKE (?)', ['%' . strtolower($keyword) . '%'])
            ->paginate(20);
    }

    public static function getOnlyAllGroceries(): array|Collection
    {
        return self::query()
            ->where('is_product', 0)
            ->get();
    }

    public static function getOnlyAllProducts(): Collection|array
    {
        return self::query()
            ->where('is_product', 1)
            ->get();
    }
}
