<?php

namespace Tighten\SolanaPhpSdk;

use Tighten\SolanaPhpSdk\Util\AccountMeta;
use Tighten\SolanaPhpSdk\Util\Buffer;

class TransactionInstruction
{
    /**
     * @var array<AccountMeta>
     */
    public $keys;
    public $programId;
    public $data;

    public function __construct(PublicKey $programId, array $keys, $data = null)
    {
        $this->programId = $programId;
        $this->keys = $keys;
        $this->data = Buffer::from($data);
    }
}
