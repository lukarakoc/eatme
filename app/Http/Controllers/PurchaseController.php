<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Grocery;
use App\Models\Purchase;
use App\Models\PurchaseGrocery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getPurchases(Request $request): JsonResponse
    {
        $currentMonth = false;
        if ($request->dateFrom === null && $request->dateTo === null) {
            $currentMonth = true;
        }
        $purchases = Purchase::getPurchasesWithGroceries($request->dateFrom, $request->dateTo, $currentMonth);
        return response()->json(['success', $purchases]);
    }

    public function storePurchase(PurchaseRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();
            if (isset($data['groceries'])) {
                $purchase = Purchase::create([
                    'total' => 0,
                    'notes' => $data['notes'],
                    'created_by' => auth()->id(),
                    'created_at' => now()
                ]);

                $total = 0.0;
                foreach ($data['groceries'] as $grocery) {
                    $grocery = json_decode($grocery, true);

                    if ($grocery['quantity'] !== null && $grocery['quantity'] > 0) {
                        $total += ($grocery['quantity'] * $grocery['unit_price']);
                        PurchaseGrocery::insert([
                            'purchase_id' => $purchase->id,
                            'grocery_id' => $grocery['id'],
                            'quantity' => $grocery['quantity'],
                            'unit_price' => $grocery['unit_price'],
                            'total' => $grocery['unit_price'] * $grocery['quantity'],
                            'created_at' => now()
                        ]);
                    }
                }

                $purchase->update(['total' => $total]);


            } else {
                return response()->json(['error' => 'Morate odabrati najmanje jednu namirnicu!'], 422);
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, $e->getMessage());
        }
        return response()->json(['success']);
    }

    public function destroy($id): JsonResponse
    {
        $purchase = Purchase::findOrFail($id);

        $purchase->delete();
        return response()->json(['success']);
    }
}
