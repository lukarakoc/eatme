<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Purchase extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public static function getSalesByMonths(): \Illuminate\Support\Collection
    {
        $year = now()->year;
         return DB::table(DB::raw('(SELECT MONTH(created_at) as month, YEAR(created_at) as year, SUM(total) as total FROM purchases WHERE YEAR(created_at) = '.$year.' GROUP BY month, year) as monthly_totals'))
            ->rightJoin(
                DB::raw('(SELECT 1 as month UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) monthsOfYear'),
                function ($join) {
                    $join->on('monthly_totals.month', '=', 'monthsOfYear.month');
                }
            )
            ->select('monthsOfYear.month as Month', DB::raw($year.' as Year'))
            ->selectRaw("IFNULL(total, 0) as TotalSum")
            ->orderBy('monthsOfYear.month')
            ->get();
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => Carbon::parse($value)->toDateTimeString()
        );
    }

    public function groceries()
    {
        return $this->belongsToMany(Grocery::class, 'purchase_groceries', 'purchase_id')->withTrashed();
    }

    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseGrocery::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getPurchasesWithGroceries($dateFrom, $dateTo, bool $currentMonth): LengthAwarePaginator
    {
        $query = self::query()
            ->with(['purchaseDetails.grocery'])
            ->orderByDesc('id');

        if ($currentMonth) {
            $month = Carbon::now()->month;
            $query->whereMonth('created_at', $month);
        } else {
            $query = self::applyDateFilters($query, $dateFrom, $dateTo);
        }

        return $query->paginate(20);
    }

    private static function applyDateFilters($query, $dateFrom, $dateTo)
    {
        return $query->where(function ($query) use ($dateFrom, $dateTo) {
            if ($dateFrom !== null) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo !== null) {
                $query->whereDate('created_at', '<=', $dateTo);
            }
        });
    }

    public static function getPurchasesThisMonthAmount()
    {
        $month = Carbon::now()->month;
        return self::query()
            ->whereMonth('created_at', $month)
            ->sum('total');
    }

    public static function getPurchasesThisYearAmount()
    {
        $year = Carbon::now()->year;
        return self::query()
            ->whereYear('created_at', $year)
            ->sum('total');
    }

}
