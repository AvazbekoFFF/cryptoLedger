<?php

namespace App\Http\Controllers\CryptoTransaction;

use App\Exceptions\DuplicateTransactionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CryptoTransaction\DepositRequest;
use App\Services\CryptoTransaction\DepositCryptoBalanceService;
use Illuminate\Http\JsonResponse;
use Throwable;

class DepositController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(
        DepositRequest              $request,
        DepositCryptoBalanceService $service
    ): JsonResponse
    {
        try {
            $transaction = $service->handle(
                $request->user()->id,
                $request->input('amount'),
                $request->input('blockchain_tx'),
            );
        } catch (DuplicateTransactionException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return response()->json([
            'message' => 'Депозит принят и ожидает подтверждения блокчейна',
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
        ], 201);
    }
}
