<?php

namespace Tighten\SolanaPhpSdk\Programs;

use Tighten\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Tighten\SolanaPhpSdk\Program;
use Tighten\SolanaPhpSdk\PublicKey;
use Tighten\SolanaPhpSdk\TransactionInstruction;
use Tighten\SolanaPhpSdk\Util\AccountMeta;

class SystemProgram extends Program
{
    const PROGRAM_INDEX_CREATE_ACCOUNT = 0;
    const PROGRAM_INDEX_TRANSFER = 2;

    /**
     * Public key that identifies the System program
     *
     * @return PublicKey
     */
    static function programId(): PublicKey
    {
        return new PublicKey('11111111111111111111111111111111');
    }

    /**
     * @param string $pubKey
     * @return array
     */
    public function getAccountInfo(string $pubKey): array
    {
        $accountResponse = $this->client->call('getAccountInfo', [$pubKey, ["encoding" => "jsonParsed"]])['value'];

        if (!$accountResponse) {
            throw new AccountNotFoundException("API Error: Account {$pubKey} not found.");
        }

        return $accountResponse;
    }

    /**
     * @param string $pubKey
     * @return float
     */
    public function getBalance(string $pubKey): float
    {
        return $this->client->call('getBalance', [$pubKey])['value'];
    }

    /**
     * @param string $transactionSignature
     * @return array
     */
    public function getConfirmedTransaction(string $transactionSignature): array
    {
        return $this->client->call('getConfirmedTransaction', [$transactionSignature]);
    }

    /**
     * NEW: This method is only available in solana-core v1.7 or newer. Please use getConfirmedTransaction for solana-core v1.6
     *
     * @param string $transactionSignature
     * @return array
     */
    public function getTransaction(string $transactionSignature): array
    {
        return $this->client->call('getTransaction', [$transactionSignature]);
    }

    /**
     * @param string $pubKey
     * @param int $limit
     * @return array
     */
    public function getTransactions(string $pubKey, int $limit = 25): array
    {
        $response = $this->client->call('getConfirmedSignaturesForAddress2', [$pubKey, ['limit' => $limit]]);

        return $response;
    }
    /**
     * Generate a transaction instruction that transfers lamports from one account to another
     *
     * @param PublicKey $fromPubkey
     * @param PublicKey $toPublicKey
     * @param int $lamports
     * @return TransactionInstruction
     */
    static public function transfer(
        PublicKey $fromPubkey,
        PublicKey $toPublicKey,
        int $lamports
    ): TransactionInstruction
    {
        $data = array_merge(
            unpack("C*", pack("V", self::PROGRAM_INDEX_TRANSFER)),
            unpack("C*", pack("P", $lamports))
        );

        $keys = [
            new AccountMeta($fromPubkey, true, true),
            new AccountMeta($toPublicKey, false, true),
        ];

        return new TransactionInstruction(
            static::programId(),
            $keys,
            $data
        );
    }

    static public function createAccount(
        PublicKey $fromPubkey,
        PublicKey $newAccountPublicKey,
        int $lamports,
        int $space,
        PublicKey $programId
    ): TransactionInstruction
    {
        $data = array_merge(
            unpack("C*", pack("V", self::PROGRAM_INDEX_CREATE_ACCOUNT)),
            unpack("C*", pack("P", $lamports)),
            unpack("C*", pack("P", $space)),
            $programId->toBytes()
        );

        $keys = [
            new AccountMeta($fromPubkey, true, true),
            new AccountMeta($newAccountPublicKey, true, true),
        ];

        return new TransactionInstruction(
            static::programId(),
            $keys,
            $data
        );
    }
}
