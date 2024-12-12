<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\PriceListResource;
use App\Models\Product\PriceList;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PriceListController extends Controller
{
    public function index(Request $request)
    {
        $query = PriceList::query()
            ->where('company_id', $request->user()->company_id);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $priceLists = $query->paginate($request->get('per_page', 10));

        return PriceListResource::collection($priceLists);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:price_lists,code,NULL,id,company_id,' . $request->user()->company_id,
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Si esta lista será la predeterminada, desmarcar las otras
        if ($request->boolean('is_default')) {
            PriceList::where('company_id', $request->user()->company_id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $priceList = new PriceList($validator->validated());
        $priceList->company_id = $request->user()->company_id;
        $priceList->save();

        return new PriceListResource($priceList);
    }

    public function show($id)
    {
        $priceList = PriceList::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        return new PriceListResource($priceList);
    }

    public function update(Request $request, $id)
    {
        $priceList = PriceList::where('company_id', $request->user()->company_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'nullable|string|max:50|unique:price_lists,code,' . $id . ',id,company_id,' . $request->user()->company_id,
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Si esta lista será la predeterminada, desmarcar las otras
        if ($request->boolean('is_default')) {
            PriceList::where('company_id', $request->user()->company_id)
                ->where('id', '!=', $id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $priceList->update($validator->validated());

        return new PriceListResource($priceList);
    }

    public function destroy($id)
    {
        $priceList = PriceList::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        if ($priceList->productPrices()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar la lista de precios porque tiene productos asociados'
            ], Response::HTTP_CONFLICT);
        }

        $priceList->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function active()
    {
        $priceLists = PriceList::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->get();

        return PriceListResource::collection($priceLists);
    }
}
