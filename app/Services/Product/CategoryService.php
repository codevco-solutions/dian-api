<?php

namespace App\Services\Product;

use App\Repositories\Contracts\Product\CategoryRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Create a new category
     */
    public function createCategory(array $data)
    {
        // Generate slug if not provided
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Set order if not provided
        if (!isset($data['order'])) {
            $siblings = $this->categoryRepository->getSubcategories($data['parent_id'] ?? null);
            $data['order'] = $siblings->count();
        }

        return $this->categoryRepository->create($data);
    }

    /**
     * Update a category
     */
    public function updateCategory(int $categoryId, array $data)
    {
        // Update slug if name changed
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->categoryRepository->update($categoryId, $data);
    }

    /**
     * Delete a category
     */
    public function deleteCategory(int $categoryId): bool
    {
        // Check if category has products
        $products = $this->categoryRepository->getProductsByCategory($categoryId);
        if ($products->isNotEmpty()) {
            throw new \Exception('Cannot delete category with associated products');
        }

        // Move subcategories to parent category
        $category = $this->categoryRepository->find($categoryId);
        $subcategories = $this->categoryRepository->getSubcategories($categoryId);
        
        foreach ($subcategories as $subcategory) {
            $this->categoryRepository->moveCategory($subcategory->id, $category->parent_id);
        }

        return $this->categoryRepository->delete($categoryId);
    }

    /**
     * Get category hierarchy
     */
    public function getCategoryHierarchy(int $categoryId): array
    {
        return $this->categoryRepository->getCategoryHierarchy($categoryId);
    }

    /**
     * Get category attributes
     */
    public function getCategoryAttributes(int $categoryId): Collection
    {
        return $this->categoryRepository->getCategoryAttributes($categoryId);
    }

    /**
     * Add category attribute
     */
    public function addCategoryAttribute(int $categoryId, array $attributeData)
    {
        // Set order if not provided
        if (!isset($attributeData['order'])) {
            $attributes = $this->getCategoryAttributes($categoryId);
            $attributeData['order'] = $attributes->count();
        }

        return $this->categoryRepository->addCategoryAttribute($categoryId, $attributeData);
    }

    /**
     * Update category attribute
     */
    public function updateCategoryAttribute(int $categoryId, int $attributeId, array $attributeData)
    {
        return $this->categoryRepository->updateCategoryAttribute($categoryId, $attributeId, $attributeData);
    }

    /**
     * Remove category attribute
     */
    public function removeCategoryAttribute(int $categoryId, int $attributeId): bool
    {
        return $this->categoryRepository->removeCategoryAttribute($categoryId, $attributeId);
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory(int $categoryId, array $filters = []): Collection
    {
        return $this->categoryRepository->getProductsByCategory($categoryId, $filters);
    }

    /**
     * Get attribute values for a product
     */
    public function getAttributeValues(int $categoryId, int $productId): Collection
    {
        return $this->categoryRepository->getAttributeValues($categoryId, $productId);
    }

    /**
     * Set attribute values for a product
     */
    public function setAttributeValues(int $categoryId, int $productId, array $values)
    {
        return $this->categoryRepository->setAttributeValues($categoryId, $productId, $values);
    }

    /**
     * Move category to new parent
     */
    public function moveCategory(int $categoryId, ?int $parentId): bool
    {
        return $this->categoryRepository->moveCategory($categoryId, $parentId);
    }

    /**
     * Reorder categories
     */
    public function reorderCategories(int $parentId, array $categoryIds): bool
    {
        return $this->categoryRepository->reorderCategories($parentId, $categoryIds);
    }

    /**
     * Get category breadcrumb
     */
    public function getCategoryBreadcrumb(int $categoryId): array
    {
        return $this->categoryRepository->getCategoryPath($categoryId);
    }

    /**
     * Get category tree
     */
    public function getCategoryTree(?int $parentId = null): array
    {
        $categories = $parentId 
            ? $this->categoryRepository->getSubcategories($parentId)
            : $this->categoryRepository->where('parent_id', null)->get();

        return $categories->map(function ($category) {
            $data = $category->toArray();
            $data['children'] = $this->getCategoryTree($category->id);
            return $data;
        })->toArray();
    }

    /**
     * Validate category structure
     */
    public function validateCategoryStructure(int $categoryId): array
    {
        $issues = [];
        $category = $this->categoryRepository->find($categoryId);

        // Check for circular references
        $ancestors = $this->categoryRepository->getAncestors($categoryId);
        if ($ancestors->contains('id', $categoryId)) {
            $issues[] = 'Circular reference detected in category hierarchy';
        }

        // Check for duplicate slugs at same level
        $siblings = $this->categoryRepository->getSiblings($categoryId);
        if ($siblings->where('slug', $category->slug)->isNotEmpty()) {
            $issues[] = 'Duplicate slug found at same category level';
        }

        // Check attribute consistency
        $attributes = $this->getCategoryAttributes($categoryId);
        $products = $this->getProductsByCategory($categoryId);

        foreach ($products as $product) {
            $values = $this->getAttributeValues($categoryId, $product->id);
            $missingAttributes = $attributes->whereNotIn('id', $values->pluck('attribute_id'));
            
            if ($missingAttributes->isNotEmpty()) {
                $issues[] = "Product {$product->id} is missing values for attributes: " . 
                    $missingAttributes->pluck('name')->implode(', ');
            }
        }

        return $issues;
    }
}
