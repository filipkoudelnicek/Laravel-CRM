<?php

namespace App\Http\Requests;

use App\Models\SupportPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupportPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id'   => 'required|integer|exists:clients,id',
            'project_id'  => ['nullable', 'integer', Rule::exists('projects', 'id')->where('client_id', $this->input('client_id'))],
            'paid_at'     => 'nullable|date',
            'title'       => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'currency'    => 'required|string|size:3',
            'period_from' => 'required|date',
            'period_to'   => 'required|date|after_or_equal:period_from',
            'status'      => 'required|in:' . implode(',', SupportPlan::STATUSES),
            'notes'       => 'nullable|string|max:2000',
        ];
    }
}
