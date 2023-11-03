<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroceryRequest;
use App\Models\Grocery;
use App\Models\Role;
use App\Traits\FileHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GroceryController extends Controller
{
    use FileHandler;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getGroceries(): JsonResponse
    {
        $groceries = Grocery::orderBy('id', 'asc')->paginate(20);
        return response()->json(['success', $groceries]);
    }

    public function store(GroceryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $image = '';
        try {

            DB::beginTransaction();
            if (isset($data['image'])) {
                $image = self::storeFile($data['image'], 'groceries');
            }
            Grocery::insert([
                'name' => $data['name'],
                'image_path' => $image,
                'is_product' => $data['is_product'] == 'true' ? 1 : 0,
                'unit_price' => $data['unit_price'],
                'created_by' => auth()->id(),
                'created_at' => now()
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, $e->getTraceAsString());
        }
        return response()->json(['success']);
    }

    public function update(GroceryRequest $request): JsonResponse
    {
        $grocery = Grocery::findOrFail($request->id);
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $grocery->name = $request->name;
            $grocery->unit_price = $request->unit_price;
            $grocery->is_product = $request->is_product == 'true' ? 1 : 0;
            if (isset($data['image'])) {
                $grocery->image_path = self::storeFile($data['image'], 'groceries');
            }

            $grocery->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, $e->getMessage());
        }
        return response()->json(['success']);
    }

    public function destroy($id): JsonResponse
    {
        $grocery = Grocery::findOrFail($id);
        if (auth()->user()->role_id != Role::ADMIN) return response()->json(['error' => 'Admin jedino može izbrisati namirnicu'], 403);

        $grocery->delete();
        return response()->json(['success']);
    }

    public function searchGroceries($keyword): JsonResponse
    {
        return response()->json(['success', Grocery::searchGroceries($keyword)]);
    }

    public function getOnlyAllGroceries(): JsonResponse
    {
        return response()->json(['success', Grocery::getOnlyAllGroceries()]);
    }

    public function getOnlyAllProducts(): JsonResponse
    {
        return response()->json(['success', Grocery::getOnlyAllProducts()]);
    }
}
