<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getDashboardData() {
        try {
            $jsonObject = [
                'salesThisMonth' => Sale::getSalesThisMonthAmount(),
                'salesThisYear' => Sale::getSalesThisYearAmount(),
                'purchasesThisMonth' => Purchase::getPurchasesThisMonthAmount(),
                'purchasesThisYear' => Purchase::getPurchasesThisYearAmount(),
                'salesByMonths' => Purchase::getSalesByMonths()
            ];
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['success', $jsonObject]);
    }
}
