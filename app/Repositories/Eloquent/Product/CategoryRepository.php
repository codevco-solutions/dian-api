<?php

namespace App\Repositories\Eloquent\Product;

use App\Models\Product\Category;
use App\Models\Product\CategoryAttribute;
use App\Models\Product\CategoryAttributeValue;
use App\Repositories\Contracts\Product\CategoryRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Support\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getSubcategories(int $categoryId): Collection
    {
        return $this->model
            ->where('parent_id', $categoryId)
            ->orderBy('order')
            ->get();
    }

    public function getCategoryHierarchy(int $categoryId): array
    {
        $category = $this->model->with('children')->findOrFail($categoryId);
        return $this->buildHierarchy($category);
    }

    protected function buildHierarchy($category): array
    {
        $result = [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'order' => $category->order,
            'children' => []
        ];

        foreach ($category->children as $child) {
            $result['children'][] = $this->buildHierarchy($child);
        }

        return $result;
    }

    public function getCategoryAttributes(int $categoryId): Collection
    {
        return CategoryAttribute::where('category_id', $categoryId)
            ->orderBy('order')
            ->get();
    }

    public function addCategoryAttribute(int $categoryId, array $attributeData)
    {
        $attributeData['category_id'] = $categoryId;
        return CategoryAttribute::create($attributeData);
    }

    public function updateCategoryAttribute(int $categoryId, int $attributeId, array $attributeData)
    {
        $attribute = CategoryAttribute::where('category_id', $categoryId)
            ->where('id', $attributeId)
            ->firstOrFail();

        $attribute->update($attributeData);
        return $attribute;
    }

    public function removeCategoryAttribute(int $categoryId, int $attributeId): bool
    {
        return CategoryAttribute::where('category_id', $categoryId)
            ->where('id', $attributeId)
            ->delete();
    }

    public function getProductsByCategory(int $categoryId, array $filters = []): Collection
    {
        $query = $this->model->findOrFail($categoryId)
            ->products()
            ->with(['brand', 'unit']);

        // Apply filters
        if (isset($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['price_min'])) {
            $query->where('base_price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('base_price', '<=', $filters['price_max']);
        }

        if (isset($filters['attributes'])) {
            foreach ($filters['attributes'] as $attributeId => $value) {
                $query->whereHas('attributeValues', function ($q) use ($attributeId, $value) {
                    $q->where('attribute_id', $attributeId)
                        ->where('value', $value);
                });
            }
        }

        return $query->get();
    }

    public function getAttributeValues(int $categoryId, int $productId): Collection
    {
        return CategoryAttributeValue::whereHas('attribute', function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
        })
        ->where('product_id', $productId)
        ->with('attribute')
        ->get();
    }

    public function setAttributeValues(int $categoryId, int $productId, array $values)
    {
        $attributes = $this->getCategoryAttributes($categoryId);
        $validAttributeIds = $attributes->pluck('id')->toArray();

        foreach ($values as $attributeId => $value) {
            if (!in_array($attributeId, $validAttributeIds)) {
                continue;
            }

            CategoryAttributeValue::updateOrCreate(
                [
                    'product_id' => $productId,
                    'attribute_id' => $attributeId
                ],
                ['value' => $value]
            );
        }
    }

    public function getCategoryPath(int $categoryId): array
    {
        $path = [];
        $category = $this->model->findOrFail($categoryId);

        while ($category) {
            array_unshift($path, [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug
            ]);
            $category = $category->parent;
        }

        return $path;
    }

    public function moveCategory(int $categoryId, ?int $parentId): bool
    {
        $category = $this->model->findOrFail($categoryId);
        
        // Validate that the new parent is not a descendant of the category
        if ($parentId) {
            $parent = $this->model->findOrFail($parentId);
            $ancestorIds = collect($this->getCategoryPath($parentId))
                ->pluck('id')
                ->toArray();

            if (in_array($categoryId, $ancestorIds)) {
                throw new \Exception('Cannot move a category to its own descendant');
            }
        }

        $category->parent_id = $parentId;
        return $category->save();
    }

    /**
     * Get all descendant categories
     */
    public function getDescendants(int $categoryId): Collection
    {
        return $this->model->findOrFail($categoryId)
            ->descendants()
            ->get();
    }

    /**
     * Get all ancestor categories
     */
    public function getAncestors(int $categoryId): Collection
    {
        return $this->model->findOrFail($categoryId)
            ->ancestors()
            ->get();
    }

    /**
     * Get categories at the same level
     */
    public function getSiblings(int $categoryId): Collection
    {
        $category = $this->model->findOrFail($categoryId);
        return $this->model
            ->where('parent_id', $category->parent_id)
            ->where('id', '!=', $categoryId)
            ->orderBy('order')
            ->get();
    }

    /**
     * Reorder categories at the same level
     */
    public function reorderCategories(int $parentId, array $order): bool
    {
        $categories = $this->model->where('parent_id', $parentId)->get();
        $orderMap = array_flip($order);

        foreach ($categories as $category) {
            if (isset($orderMap[$category->id])) {
                $category->order = $orderMap[$category->id];
                $category->save();
            }
        }

        return true;
    }
}
