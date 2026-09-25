<?php

namespace App\Http\Requests\Admin;

use App\Enums\AdoptionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdoptionStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(AdoptionStatus::class)],
        ];
    }
}
