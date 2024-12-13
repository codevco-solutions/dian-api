<?php

namespace App\Repositories\Contracts\Product;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getSubcategories(int $categoryId): Collection;
    public function getCategoryHierarchy(int $categoryId): array;
    public function getCategoryAttributes(int $categoryId): Collection;
    public function addCategoryAttribute(int $categoryId, array $attributeData);
    public function updateCategoryAttribute(int $categoryId, int $attributeId, array $attributeData);
    public function removeCategoryAttribute(int $categoryId, int $attributeId): bool;
    public function getProductsByCategory(int $categoryId, array $filters = []): Collection;
    public function getAttributeValues(int $categoryId, int $productId): Collection;
    public function setAttributeValues(int $categoryId, int $productId, array $values);
    public function getCategoryPath(int $categoryId): array;
    public function moveCategory(int $categoryId, ?int $parentId): bool;
}
