<?php

declare(strict_types=1);

namespace Lightit\Authentication\App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lightit\Authentication\Domain\DataTransferObjects\LoginResponseDto;

/**
 * @property LoginResponseDto $resource
 */
class AuthTokenResource extends JsonResource
{
    /**
     * @return array{access_token: string, token_type: string, expires_in: int}
     */
    public function toArray(Request $request): array
    {
        /** @var LoginResponseDto $dto */
        $dto = $this->resource;

        return [
            'access_token' => $dto->accessToken,
            'token_type' => $dto->tokenType,
            'expires_in' => $dto->expiresIn,
        ];
    }
}
