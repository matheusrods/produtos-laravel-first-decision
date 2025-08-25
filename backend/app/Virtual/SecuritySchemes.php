<?php

namespace App\Virtual;

/**
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Insira o token obtido no login. Ex: Bearer {seu_token}"
 * )
 */
class SecuritySchemes {}