<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function createdAt() : Attribute {
        return Attribute::make(
            get: static fn (string $value) => Carbon::parse($value)->toDateTimeString()
        );
    }

    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public static function search($keyword): LengthAwarePaginator
    {
        return self::query()
            ->where('amount', '=', $keyword)
            ->orWhereRaw('LOWER(notes) LIKE (?)', ['%' . strtolower($keyword) . '%'])
            ->paginate(20);
    }

    public static function getExpensesWithType(mixed $dateFrom, mixed $dateTo, bool $currentMonth, mixed $type): LengthAwarePaginator
    {
        $query = self::query()
            ->with(['expenseType'])
            ->orderByDesc('id');

        if ($currentMonth) {
            $month = Carbon::now()->month;
            $query->whereMonth('created_at', $month);
        } else {
            $query = self::applyDateFilters($query, $dateFrom, $dateTo);
        }

        if ($type !== null) {
            $query->whereHas('expenseType', function ($query) use ($type) {
                $query->where('expense_type_id', $type);
            });
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
}
