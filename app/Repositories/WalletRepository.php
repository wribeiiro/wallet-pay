<?php

namespace App\Repositories;

use App\Models\Wallet;

class WalletRepository
{
    /**
     *
     * @param Wallet $wallet
     * @param float $value
     * @return boolean
     */
    public function balanceIsValid(Wallet $wallet, float $value): bool
    {
        return $wallet->balance >= $value;
    }

    /**
     *
     * @param string $userId
     * @return Wallet|null
     */
    public function walletExists(string $userId): Wallet|null
    {
        return Wallet::where('user_id', $userId)->first();
    }

    /**
     *
     * @param Wallet $wallet
     * @param float $value
     * @return void
     */
    public function addMoney(Wallet $wallet, float $value): void
    {
        $newValue = [
            'balance' => $wallet->balance + $value
        ];

        $wallet->update($newValue);
    }

    /**
     *
     * @param Wallet $wallet
     * @param float $value
     * @return void
     */
    public function removeMoney(Wallet $wallet, float $value): void
    {
        $newValue = [
            'balance' => $wallet->balance - $value
        ];

        $wallet->update($newValue);
    }
}
