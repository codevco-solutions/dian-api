<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('company_id', $request->user()->company_id)
            ->with(['category', 'measurementUnit', 'prices.priceList', 'taxes']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $products = $query->paginate($request->get('per_page', 10));

        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|exists:product_categories,id',
            'measurement_unit_id' => 'required|exists:measurement_units,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:products,code,NULL,id,company_id,' . $request->user()->company_id,
            'barcode' => 'nullable|string|max:50|unique:products,barcode,NULL,id,company_id,' . $request->user()->company_id,
            'description' => 'nullable|string',
            'type' => 'required|in:product,service',
            'base_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'metadata' => 'nullable|json',
            'taxes' => 'array',
            'taxes.*.tax_id' => 'required|exists:taxes,id',
            'taxes.*.rate' => 'required|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product = new Product($validator->validated());
        $product->company_id = $request->user()->company_id;
        $product->save();

        // Asociar impuestos
        if ($request->has('taxes')) {
            foreach ($request->get('taxes') as $tax) {
                $product->taxes()->attach($tax['tax_id'], ['rate' => $tax['rate']]);
            }
        }

        return new ProductResource($product->load(['category', 'measurementUnit', 'taxes']));
    }

    public function show($id)
    {
        $product = Product::where('company_id', auth()->user()->company_id)
            ->with(['category', 'measurementUnit', 'prices.priceList', 'taxes'])
            ->findOrFail($id);

        return new ProductResource($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('company_id', $request->user()->company_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|exists:product_categories,id',
            'measurement_unit_id' => 'sometimes|required|exists:measurement_units,id',
            'name' => 'sometimes|required|string|max:255',
            'code' => 'nullable|string|max:50|unique:products,code,' . $id . ',id,company_id,' . $request->user()->company_id,
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $id . ',id,company_id,' . $request->user()->company_id,
            'description' => 'nullable|string',
            'type' => 'sometimes|required|in:product,service',
            'base_price' => 'sometimes|required|numeric|min:0',
            'tax_rate' => 'sometimes|required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'metadata' => 'nullable|json',
            'taxes' => 'array',
            'taxes.*.tax_id' => 'required|exists:taxes,id',
            'taxes.*.rate' => 'required|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product->update($validator->validated());

        // Actualizar impuestos
        if ($request->has('taxes')) {
            $product->taxes()->detach();
            foreach ($request->get('taxes') as $tax) {
                $product->taxes()->attach($tax['tax_id'], ['rate' => $tax['rate']]);
            }
        }

        return new ProductResource($product->load(['category', 'measurementUnit', 'taxes']));
    }

    public function destroy($id)
    {
        $product = Product::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        // Aquí podrías agregar validaciones adicionales antes de eliminar
        // Por ejemplo, verificar si el producto está siendo usado en documentos

        $product->taxes()->detach();
        $product->prices()->delete();
        $product->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function active()
    {
        $products = Product::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->with(['category', 'measurementUnit', 'taxes'])
            ->get();

        return ProductResource::collection($products);
    }
}
