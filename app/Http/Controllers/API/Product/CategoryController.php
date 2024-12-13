<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Services\Product\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Create a new category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer|min:0'
        ]);

        try {
            $category = $this->categoryService->createCategory($request->all());

            return response()->json([
                'message' => 'Category created successfully',
                'data' => $category
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update a category
     */
    public function update(Request $request, $categoryId)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $categoryId,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer|min:0'
        ]);

        try {
            $category = $this->categoryService->updateCategory($categoryId, $request->all());

            return response()->json([
                'message' => 'Category updated successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Delete a category
     */
    public function destroy($categoryId)
    {
        try {
            $this->categoryService->deleteCategory($categoryId);

            return response()->json([
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get category hierarchy
     */
    public function getHierarchy($categoryId)
    {
        $hierarchy = $this->categoryService->getCategoryHierarchy($categoryId);

        return response()->json([
            'message' => 'Category hierarchy retrieved successfully',
            'data' => $hierarchy
        ]);
    }

    /**
     * Get category attributes
     */
    public function getAttributes($categoryId)
    {
        $attributes = $this->categoryService->getCategoryAttributes($categoryId);

        return response()->json([
            'message' => 'Category attributes retrieved successfully',
            'data' => $attributes
        ]);
    }

    /**
     * Add category attribute
     */
    public function addAttribute(Request $request, $categoryId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,boolean,select,multiselect',
            'options' => 'required_if:type,select,multiselect|array',
            'required' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0'
        ]);

        try {
            $attribute = $this->categoryService->addCategoryAttribute($categoryId, $request->all());

            return response()->json([
                'message' => 'Category attribute added successfully',
                'data' => $attribute
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update category attribute
     */
    public function updateAttribute(Request $request, $categoryId, $attributeId)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:text,number,boolean,select,multiselect',
            'options' => 'required_if:type,select,multiselect|array',
            'required' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0'
        ]);

        try {
            $attribute = $this->categoryService->updateCategoryAttribute(
                $categoryId,
                $attributeId,
                $request->all()
            );

            return response()->json([
                'message' => 'Category attribute updated successfully',
                'data' => $attribute
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove category attribute
     */
    public function removeAttribute($categoryId, $attributeId)
    {
        try {
            $this->categoryService->removeCategoryAttribute($categoryId, $attributeId);

            return response()->json([
                'message' => 'Category attribute removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get products by category
     */
    public function getProducts(Request $request, $categoryId)
    {
        $filters = $request->only([
            'brand_id',
            'status',
            'price_min',
            'price_max',
            'attributes'
        ]);

        $products = $this->categoryService->getProductsByCategory($categoryId, $filters);

        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $products
        ]);
    }

    /**
     * Get product attribute values
     */
    public function getProductAttributes($categoryId, $productId)
    {
        $values = $this->categoryService->getAttributeValues($categoryId, $productId);

        return response()->json([
            'message' => 'Product attributes retrieved successfully',
            'data' => $values
        ]);
    }

    /**
     * Set product attribute values
     */
    public function setProductAttributes(Request $request, $categoryId, $productId)
    {
        $request->validate([
            'values' => 'required|array'
        ]);

        try {
            $this->categoryService->setAttributeValues($categoryId, $productId, $request->values);

            return response()->json([
                'message' => 'Product attributes updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Move category
     */
    public function moveCategory(Request $request, $categoryId)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        try {
            $this->categoryService->moveCategory($categoryId, $request->parent_id);

            return response()->json([
                'message' => 'Category moved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Reorder categories
     */
    public function reorderCategories(Request $request, $parentId)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|exists:categories,id'
        ]);

        try {
            $this->categoryService->reorderCategories($parentId, $request->order);

            return response()->json([
                'message' => 'Categories reordered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get category tree
     */
    public function getCategoryTree(Request $request)
    {
        $tree = $this->categoryService->getCategoryTree($request->parent_id);

        return response()->json([
            'message' => 'Category tree retrieved successfully',
            'data' => $tree
        ]);
    }

    /**
     * Validate category structure
     */
    public function validateStructure($categoryId)
    {
        $issues = $this->categoryService->validateCategoryStructure($categoryId);

        return response()->json([
            'message' => 'Category structure validated',
            'data' => [
                'valid' => empty($issues),
                'issues' => $issues
            ]
        ]);
    }
}
