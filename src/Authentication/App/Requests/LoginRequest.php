<?php

declare(strict_types=1);

namespace Lightit\Authentication\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Email;
use Illuminate\Validation\Rules\Password;
use Lightit\Authentication\Domain\DataTransferObjects\LoginRequestDto;

class LoginRequest extends FormRequest
{
    private const string EMAIL = 'email';

    private const string PASSWORD = 'password';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            self::EMAIL => ['required', Email::default()],
            self::PASSWORD => ['required', Password::default()],
        ];
    }

    public function toDto(): LoginRequestDto
    {
        return new LoginRequestDto(
            email: $this->string(self::EMAIL)->toString(),
            password: $this->string(self::PASSWORD)->toString()
        );
    }
}
