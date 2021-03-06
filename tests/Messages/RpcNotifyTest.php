<?php

namespace WyriHaximus\React\Tests\ChildProcess\Messenger\Messages;

use PHPUnit\Framework\TestCase;
use WyriHaximus\React\ChildProcess\Messenger\Messages\Payload;
use WyriHaximus\React\ChildProcess\Messenger\Messages\RpcNotify;

class RpcNotifyTest extends TestCase
{
    public function testBasic()
    {
        $payload = new Payload([
            'foo' => 'bar',
        ]);
        $message = new RpcNotify('abc', $payload);

        $this->assertSame($payload, $message->getPayload());

        $this->assertEquals('{"type":"rpc_notify","uniqid":"abc","payload":{"foo":"bar"}}', \json_encode($message));

        $outstandingCall = $this->prophesize('WyriHaximus\React\ChildProcess\Messenger\OutstandingCall');
        $messenger = $this->prophesize('WyriHaximus\React\ChildProcess\Messenger\Messenger');
        $messenger->getOutstandingCall('abc')->shouldBeCalled()->wilLReturn($outstandingCall->reveal());

        $message->handle($messenger->reveal(), '');
    }
}
