<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="Usuário",
 *     description="Schema de um usuário autenticado",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Usuário Teste"),
 *     @OA\Property(property="email", type="string", example="teste@teste.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-25T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-25T12:00:00Z")
 * )
 */
class User {}