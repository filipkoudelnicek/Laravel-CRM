<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // handled by policy in controller
    }

    public function rules(): array
    {
        return [
            'client_id'     => 'required|integer|exists:clients,id',
            'project_id'    => ['nullable', 'integer', Rule::exists('projects', 'id')->where('client_id', $this->input('client_id'))],
            'issued_at'     => 'nullable|date',
            'due_at'        => 'nullable|date',
            'paid_at'       => 'nullable|required_if:status,paid|date',
            'status'        => 'required|in:' . implode(',', Invoice::STATUSES),
            'currency'      => 'required|string|size:3',
            'tax_rate'      => 'required|numeric|min:0|max:100',
            'notes'         => 'nullable|string|max:2000',

            // items (inline)
            'items'              => 'required|array|min:1',
            'items.*.name'       => 'required|string|max:255',
            'items.*.description'=> 'nullable|string|max:1000',
            'items.*.qty'        => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'          => 'Faktura musí mít alespoň jednu položku.',
            'project_id.exists'       => 'Projekt musí patřit vybranému klientovi.',
            'paid_at.required_if'     => 'U zaplacené faktury vyplňte datum úhrady.',
            'items.min'               => 'Faktura musí mít alespoň jednu položku.',
            'items.*.name.required'   => 'Název položky je povinný.',
            'items.*.qty.required'    => 'Množství je povinné.',
            'items.*.unit_price.required' => 'Jednotková cena je povinná.',
        ];
    }
}
