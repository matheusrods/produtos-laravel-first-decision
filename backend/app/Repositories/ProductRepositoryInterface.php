<?php

namespace App\Repositories;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function getAll();
    public function create(array $data): Product;
    public function find(int $id): ?Product;
    public function update(Product $product, array $data): Product;
    public function delete(Product $product): void;
}