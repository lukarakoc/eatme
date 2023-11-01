<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getExpenses(Request $request): JsonResponse
    {
        $currentMonth = false;
        if ($request->dateFrom === null && $request->dateTo === null) {
            $currentMonth = true;
        }
        $expenses = Expense::getExpensesWithType($request->dateFrom, $request->dateTo, $currentMonth, $request->type);
        return response()->json(['success', $expenses]);
    }

    public function storeExpense(ExpenseRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            Expense::create([
                'expense_type_id' => $data['type'],
                'notes' => $data['notes'],
                'amount' => $data['amount'],
                'created_at' => now(),
                'created_by' => auth()->id()
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, $e->getMessage());
        }
        return response()->json(['success']);
    }

    public function destroy($id): JsonResponse
    {
        $expense = Expense::findOrFail($id);

        $expense->delete();
        return response()->json(['success']);
    }

    public function searchExpenses($keyword): JsonResponse
    {
        return response()->json(['success', Expense::search($keyword)]);
    }

    public function getAllTypes(): JsonResponse
    {
        return response()->json(['success', ExpenseType::all()]);
    }
}
