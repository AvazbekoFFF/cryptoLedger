<?php

namespace App\Http\Requests\CryptoTransaction;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount'        => ['required', 'numeric', 'gt:0'],
            'blockchain_tx' => ['required', 'string', 'regex:/^0x[0-9a-fA-F]{64}$/'],
        ];
    }
    //TODO использовать тут нужно DTO
}
