<?php

namespace App\Http\Services;

use App\Repositories\ProductRepositoryInterface;
use App\Models\Product;

class ProductService
{
    public function __construct(private ProductRepositoryInterface $productRepository) {}

    public function getAll()
    {
        return $this->productRepository->getAll();
    }

    public function create(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    public function find(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function update(Product $product, array $data): Product
    {
        return $this->productRepository->update($product, $data);
    }

    public function delete(Product $product): void
    {
        $this->productRepository->delete($product);
    }
}
