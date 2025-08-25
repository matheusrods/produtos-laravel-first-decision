<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Http\Services\ProductService;
use App\Models\Product;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    /**
     * @OA\Get(
     *     path="/products",
     *     summary="Listar todos os produtos",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de produtos retornada com sucesso",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     ),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function index()
    {
        $products = $this->productService->getAll();
        return ProductResource::collection($products);
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Criar um novo produto",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produto criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function store(ProductStoreRequest $request)
    {
        $product = $this->productService->create($request->validated());

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Produto criado com sucesso'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Exibir um produto",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do produto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto retornado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=404, description="Produto não encontrado")
     * )
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     summary="Atualizar um produto",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do produto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=404, description="Produto não encontrado")
     * )
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $updated = $this->productService->update($product, $request->validated());

        return new ProductResource($updated);
    }

    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     summary="Remover um produto",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do produto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto removido com sucesso"
     *     ),
     *     @OA\Response(response=404, description="Produto não encontrado")
     * )
     */
    public function destroy(Product $product)
    {
        $this->productService->delete($product);

        return response()->json(['message' => 'Produto removido com sucesso']);
    }
}