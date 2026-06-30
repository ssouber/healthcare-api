<?php

declare(strict_types=1);

namespace Lightit\Authentication\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            self::EMAIL => ['required', Rule::email()->strict()],
            self::PASSWORD => ['required'],
        ];
    }

    /**
     * @return array{email: string, password: string}
     */
    public function credentials(): array
    {
        return [
            'email' => $this->string(self::EMAIL)->toString(),
            'password' => $this->string(self::PASSWORD)->toString(),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
