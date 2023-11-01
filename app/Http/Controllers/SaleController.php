<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use App\Models\Sale;
use App\Models\SaleGrocery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getSales(Request $request): JsonResponse
    {
        $currentMonth = false;
        if ($request->dateFrom === null && $request->dateTo === null) {
            $currentMonth = true;
        }
        $sales = Sale::getSalesWithGroceries($request->dateFrom, $request->dateTo, $currentMonth, $request->client);
        return response()->json(['success', $sales]);
    }

    public function storeSale(SaleRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();
            if (isset($data['groceries'])) {
                $sale = Sale::create([
                    'total' => 0,
                    'notes' => $data['notes'],
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'client_id' => $data['client']
                ]);

                $total = 0.0;
                foreach ($data['groceries'] as $grocery) {
                    $grocery = json_decode($grocery, true);

                    if ($grocery['quantity'] !== null && $grocery['quantity'] > 0) {
                        $total += ($grocery['quantity'] * $grocery['unit_price']);
                        SaleGrocery::insert([
                            'sale_id' => $sale->id,
                            'grocery_id' => $grocery['id'],
                            'quantity' => $grocery['quantity'],
                            'unit_price' => $grocery['unit_price'],
                            'total' => $grocery['unit_price'] * $grocery['quantity'],
                            'created_at' => now()
                        ]);
                    }
                }

                $sale->update(['total' => $total]);


            } else {
                return response()->json(['error' => 'Morate odabrati najmanje jedan proizvod!'], 422);
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
        $sale = Sale::findOrFail($id);

        $sale->delete();
        return response()->json(['success']);
    }
}
