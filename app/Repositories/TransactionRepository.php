<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\TransactionValidationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TransactionRepository
{
    public function __construct(
        private NotificationService $serviceNotification,
        private WalletRepository $walletRepository,
        private TransactionValidationService $transactionValidationService
    ) { }

    /**
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Transaction::all();
    }

    /**
     *
     * @param array $data
     * @return Transaction
     */
    public function executeTransaction(array $data): Transaction
    {
        $this->transactionValidationService->validate($data);

        return $this->createTransaction(
            User::find($data['payer_id']),
            User::find($data['payee_id']),
            $data['value']
        );
    }

    /**
     *
     * @param User $payer
     * @param User $payee
     * @param float $value
     * @return Transaction
     */
    public function createTransaction(
        User $payer,
        User $payee,
        float $value
    ): Transaction {

        $payload = [
            'value' => $value,
            'payer_wallet_id' => $payer->wallet->id,
            'payee_wallet_id' => $payee->wallet->id
        ];

        return DB::transaction(function() use ($payer, $payee, $payload) {
            $transaction = Transaction::create($payload);

            $this->walletRepository->removeMoney($payer->wallet, $payload['value']);
            $this->walletRepository->addMoney($payee->wallet, $payload['value']);
            $this->serviceNotification->notify();

            return $transaction;
        });
    }
}
