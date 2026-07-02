<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\DataTransferObjects\DoctorDto;

class StoreDoctorRequest extends FormRequest
{
    public const string NAME = 'name';

    public const string CLINICS = 'clinics';

    public function rules(): array
    {
        return [
            self::NAME => ['required', 'string', 'min:4', 'max:255'],
            self::CLINICS => ['sometimes', 'array'],
            self::CLINICS . '.*' => [Rule::numeric()->integer()],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->hasAny(self::CLINICS . '.*')) {
                return;
            }

            $clinics = $this->input(self::CLINICS);

            if (! is_array($clinics) || $clinics === []) {
                return;
            }

            $existingCount = Clinic::query()->whereIn('id', $clinics)->count();

            if ($existingCount !== count($clinics)) {
                $validator->errors()->add(self::CLINICS, 'The clinics are invalid.');
            }
        });
    }

    public function toDto(): DoctorDto
    {
        return new DoctorDto(
            name: $this->string(self::NAME)->toString(),
            clinics: $this->array(self::CLINICS)
        );
    }
}
