<?php

namespace App\Http\Requests\CryptoTransaction;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0'],
        ];
    }
    //TODO использовать тут нужно DTO
}
