<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;

class Sale extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * @param bool $currentMonth
     * @param $dateFrom
     * @param $dateTo
     * @param $grocery
     * @return Builder
     */
    public static function getSalesWithGroceriesQuery(bool $currentMonth, $dateFrom, $dateTo, $client): Builder
    {
        $query = self::query()
            ->with(['saleDetails.grocery' => function ($query) {
                $query->select('id', 'name', 'unit_price', 'image_path', 'is_product');
            }])
            ->with('client')
            ->orderByDesc('id');

        if ($currentMonth) {
            $month = Carbon::now()->month;
            $query->whereMonth('created_at', $month);
        } else {
            if ($dateFrom !== null) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo !== null) {
                $query->whereDate('created_at', '<=', $dateTo);
            }
        }

        if ($client !== null) {
            $query->where('client_id', '=', $client);
        }
        return $query;
    }

    public static function getSalesThisMonthAmount()
    {
        $month = Carbon::now()->month;
        return self::query()
            ->whereMonth('created_at', $month)
            ->sum('total');
    }

    public static function getSalesThisYearAmount()
    {
        $year = Carbon::now()->year;
        return self::query()
            ->whereYear('created_at', $year)
            ->sum('total');
    }

    protected function createdAt() : Attribute {
        return Attribute::make(
            get: static fn (string $value) => Carbon::parse($value)->toDateTimeString()
        );
    }

    public function groceries()
    {
        return $this->belongsToMany(Grocery::class, 'sale_groceries', 'sale_id')->withTrashed();
    }

    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleGrocery::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public static function getSalesWithGroceries($dateFrom, $dateTo, bool $currentMonth, $client): LengthAwarePaginator
    {
        $query = self::getSalesWithGroceriesQuery($currentMonth, $dateFrom, $dateTo, $client);

        return $query->paginate(20);
    }

    public static function exportSalesWithGroceries($dateFrom, $dateTo, bool $currentMonth, $grocery): array|Collection
    {
        $query = self::getSalesWithGroceriesQuery($currentMonth, $dateFrom, $dateTo, $grocery);

        return $query->get();
    }
}
