<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductCategoryResource;
use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductCategory::query()
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

        $categories = $query->paginate($request->get('per_page', 10));

        return ProductCategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:product_categories,code,NULL,id,company_id,' . $request->user()->company_id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $category = new ProductCategory($validator->validated());
        $category->company_id = $request->user()->company_id;
        $category->save();

        return new ProductCategoryResource($category);
    }

    public function show($id)
    {
        $category = ProductCategory::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        return new ProductCategoryResource($category);
    }

    public function update(Request $request, $id)
    {
        $category = ProductCategory::where('company_id', $request->user()->company_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'nullable|string|max:50|unique:product_categories,code,' . $id . ',id,company_id,' . $request->user()->company_id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $category->update($validator->validated());

        return new ProductCategoryResource($category);
    }

    public function destroy($id)
    {
        $category = ProductCategory::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar la categoría porque tiene productos asociados'
            ], Response::HTTP_CONFLICT);
        }

        $category->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function active()
    {
        $categories = ProductCategory::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->get();

        return ProductCategoryResource::collection($categories);
    }
}
