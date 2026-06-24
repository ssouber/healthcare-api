<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public const string NAME = 'name';

    public function rules(): array
    {
        return [
            self::NAME => ['required', Rule::string(), 'min:4', 'max:255'],
        ];
    }

    public function getName(): string
    {
        return $this->string(self::NAME)->toString();
    }
}
