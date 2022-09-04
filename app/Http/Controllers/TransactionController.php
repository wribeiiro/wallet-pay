<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionRepository $transactionRepository
    ) { }

    /**
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->transactionRepository->getAll(),
            'message' => 'OK',
            'code' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

     /**
     *
     * @param Transaction $transaction
     * @return JsonResponse
     * @throws Exception
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $transactionResponse = [
            "code" => Response::HTTP_OK,
            "message" => "OK",
            "data" => null
        ];

        try {
            $transactionResponse["data"] = $transaction;
        } catch (\Exception $error) {
            $transactionResponse["message"] = $error->getMessage();
            $transactionResponse["code"] = $error->getCode();
        }

        return response()->json($transactionResponse, $transactionResponse["code"]);
    }

    /**
     *
     * @param TransactionRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(TransactionRequest $request): JsonResponse
    {
        $transactionResponse = [
            "code" => Response::HTTP_CREATED,
            "message" => "Transaction was created with success.",
            "data" => null
        ];

        try {
            $transactionResult = $this->transactionRepository->executeTransaction([
                "payer_id" => $request->payer_id,
                "payee_id" => $request->payee_id,
                "value" => $request->value
            ]);

            $transactionResponse["data" ] = [
                "id" => $transactionResult->id,
                "payer" => $transactionResult->walletPayer,
                "payee" => $transactionResult->walletPayee,
                "value" => $transactionResult->value,
                "created_at" => $transactionResult->created_at
            ];

        } catch (\Exception $error) {
            $transactionResponse["message"] = $error->getMessage();
            $transactionResponse["code"] = $error->getCode();
        }

        return response()->json($transactionResponse, $transactionResponse["code"]);
    }

    /**
     *
     * @param Transaction $transaction
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        $responsePayload = [
            "message" => "Transaction was deleted with success.",
            "code" => Response::HTTP_NO_CONTENT,
            "data" => null
        ];

        try {
            $transaction->delete();
        } catch (\Exception $error) {
            $responsePayload["message"] = $error->getMessage();
            $responsePayload["code"] = $error->getCode();
        }

        return response()->json($responsePayload, $responsePayload["code"]);
    }
}
