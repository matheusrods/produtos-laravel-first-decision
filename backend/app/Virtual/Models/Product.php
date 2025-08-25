<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Produto",
 *     description="Schema do Produto para documentação",
 *     required={"name","price","stock"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Produto Teste"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Descrição do produto"),
 *     @OA\Property(property="price", type="number", format="float", example=99.90),
 *     @OA\Property(property="stock", type="integer", example=10),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-25T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-25T12:00:00Z")
 * )
 */
class Product {}