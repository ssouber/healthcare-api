<?php

declare(strict_types=1);

namespace Lightit\Authentication\App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lightit\Authentication\Domain\DataTransferObjects\LoginResponseDto;

/**
 * @mixin LoginResponseDto
 */
class AuthTokenResource extends JsonResource
{
    /**
     * @return array{access_token: string, token_type: string, expires_in: int}
     */
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresIn,
        ];
    }
}
