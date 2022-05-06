<?php

namespace Tighten\SolanaPhpSdk\Tests\Feature;

use Tighten\SolanaPhpSdk\Programs\SystemProgram;
use Tighten\SolanaPhpSdk\SolanaRpcClient;
use Tighten\SolanaPhpSdk\Tests\TestCase;

class SolanaRpcClientTest extends TestCase
{
    /** @test */
    public function it_generates_random_key()
    {
        $client = new SolanaRpcClient('abc.com');
        $rpc1 = $client->buildRpc('doStuff', []);
        $rpc2 = $client->buildRpc('doStuff', []);

        $client = new SolanaRpcClient('abc.com');
        $rpc3 = $client->buildRpc('doStuff', []);
        $rpc4 = $client->buildRpc('doStuff', []);

        $this->assertEquals($rpc1['id'], $rpc2['id']);
        $this->assertEquals($rpc3['id'], $rpc4['id']);
        $this->assertNotEquals($rpc1['id'], $rpc4['id']);
    }

    /** @test */
    public function it_creates_account()
    {
        $client = new SolanaRpcClient(SolanaRpcClient::TESTNET_ENDPOINT);

        $solana = new SystemProgram($client);

        $instruction = $solana::createAccount(
            SystemProgram::programId(),
            SystemProgram::programId(),
            2039280,
            165,
            SystemProgram::programId()
        );

        $this->assertEquals("11119os1e9qSs2u7TsThXqkBSRUo9x7kpbdqtNNbTeaxHGPdWbvoHsks9hpp6mb2ed1NeB",
            $instruction->data->toBase58String());
    }
}
