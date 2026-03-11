<?php

namespace App\Http\Controllers\CryptoTransaction;

use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CryptoTransaction\WithdrawRequest;
use App\Services\CryptoTransaction\WithdrawCryptoBalanceService;
use Illuminate\Http\JsonResponse;
use Throwable;

class WithdrawController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(
        WithdrawRequest              $request,
        WithdrawCryptoBalanceService $service
    ): JsonResponse
    {
        try {
            $transaction = $service->handle(
                $request->user()->id,
                $request->input('amount'),
            );
        } catch (InsufficientBalanceException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Вывод принят и отправляется в блокчейн',
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
        ], 201);
    }
}
