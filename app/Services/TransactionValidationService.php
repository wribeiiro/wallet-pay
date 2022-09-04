<?php

namespace App\Services;

use App\Enums\MockServiceStatusEnum;
use App\Enums\UserTypeEnum;
use App\Exceptions\ShortCashException;
use App\Exceptions\InvalidTransactionException;
use App\Exceptions\WalletNotExistsException;
use App\Exceptions\AuthServiceUnavailableException;
use App\Repositories\WalletRepository;
use App\Models\User;
use Illuminate\Http\Response;

class TransactionValidationService
{
    public function __construct(
        private WalletRepository $walletRepository,
        private TransactionAuthorizeService $serviceAuthorizeTransaction
    ) { }

    /**
     *
     * @param array $data
     * @return void
     * @throws AuthServiceUnavailableException
     * @throws InvalidTransactionException
     * @throws WalletNotExistsException
     * @throws ShortCashException
     *
     */
    public function validate(array $data): void
    {
        $statusAuthorizeTransaction = $this->serviceAuthorizeTransaction->statusAuthorizeTransaction();

        if ($statusAuthorizeTransaction['message'] !== MockServiceStatusEnum::Authorized->value) {
            throw new AuthServiceUnavailableException("Authorize Service is Unavailable.", $statusAuthorizeTransaction['code']);
        }

        $wallet = $this->walletRepository->walletExists($data['payer_id']);
        if ($wallet === null) {
            throw new WalletNotExistsException("User wallet does not exists.", Response::HTTP_NOT_FOUND);
        }

        if (!$this->walletRepository->balanceIsValid($wallet, $data['value'])) {
            throw new ShortCashException("You don't have enough amount to transfer.", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $userPayer = User::find($data['payer_id']);
        if ($userPayer->user_type === UserTypeEnum::Retailer->value) {
            throw new InvalidTransactionException("Retailer isn't authorized to make transactions.", Response::HTTP_UNAUTHORIZED);
        }

        if ($data['payer_id'] === $data['payee_id']) {
            throw new InvalidTransactionException("You cannot transfer to yourself.", Response::HTTP_UNAUTHORIZED);
        }
    }
}
