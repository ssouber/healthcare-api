<?php

declare(strict_types=1);

namespace Lightit\Clinics\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lightit\Clinics\Domain\DataTransferObjects\StoreClinicDto;
use Lightit\Doctors\Domain\Models\Doctor;

class StoreClinicRequest extends FormRequest
{
    public const string NAME = 'name';

    public const string ADDRESS = 'address';

    public const string DOCTORS = 'doctors';

    public function rules(): array
    {
        return [
            self::NAME => ['required', 'string', 'min:4', 'max:255'],
            self::ADDRESS => ['required', 'string', 'max:255'],
            self::DOCTORS => ['sometimes', 'array', Rule::exists(Doctor::class, 'id')],
            self::DOCTORS . '.*' => [Rule::numeric()->integer()],
        ];
    }

    public function toDto(): StoreClinicDto
    {
        return new StoreClinicDto(
            name: $this->string(self::NAME)->toString(),
            address: $this->string(self::ADDRESS)->toString(),
            doctors: $this->array(self::DOCTORS),
        );
    }
}
