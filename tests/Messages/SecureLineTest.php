<?php

namespace WyriHaximus\React\Tests\ChildProcess\Messenger\Messages;

use PHPUnit\Framework\TestCase;
use WyriHaximus\React\ChildProcess\Messenger\Messages\LineInterface;
use WyriHaximus\React\ChildProcess\Messenger\Messages\Payload;
use WyriHaximus\React\ChildProcess\Messenger\Messages\Rpc;
use WyriHaximus\React\ChildProcess\Messenger\Messages\SecureLine;

class SecureLineTest extends TestCase
{
    const KEY = 'abc';

    public function providerBasic()
    {
        return [
            [
                new Rpc(
                    'foo',
                    new Payload([
                        'bar' => 'baz',
                    ]),
                    1234567890
                ),
                '{"type":"secure","line":"{\"type\":\"rpc\",\"uniqid\":1234567890,\"target\":\"foo\",\"payload\":{\"bar\":\"baz\"}}","signature":"\/HYFhnhlrlzYUjEjb7WFIBoNeZeIoSSLagFBj1GbzlY="}' . LineInterface::EOL,
                '{"type":"rpc","uniqid":1234567890,"target":"foo","payload":{"bar":"baz"}}',
            ],
            [
                new Rpc(
                    'foo',
                    new Payload([
                        'bar',
                        'baz',
                    ]),
                    1234567890
                ),
                '{"type":"secure","line":"{\"type\":\"rpc\",\"uniqid\":1234567890,\"target\":\"foo\",\"payload\":[\"bar\",\"baz\"]}","signature":"r7TvJ\/AuvAY7dKZ+7wQyI0PdyLivANZzPB35j8Xuyps="}' . LineInterface::EOL,
                '{"type":"rpc","uniqid":1234567890,"target":"foo","payload":["bar","baz"]}',
            ],
        ];
    }

    /**
     * @dataProvider providerBasic
     * @param mixed $output
     * @param mixed $lineString
     */
    public function testBasic(\JsonSerializable $input, $output, $lineString)
    {
        $line = new SecureLine($input, [
            'key' => static::KEY,
        ]);
        $this->assertEquals($output, (string)$line);

        $stringLine = SecureLine::fromLine(\json_decode((string)$line, true), [
            'key' => static::KEY,
        ]);
        $this->assertEquals($lineString, \json_encode($stringLine));
    }

    /**
     * @expectedException           \Exception
     * @expectedExceptionMessage    Signature mismatch!
     */
    public function testSignatureMismatch()
    {
        $line = '{"type":"secure","line":"{\"type\":\"rpc\",\"uniqid\":1234567890,\"target\":\"foo\",\"payload\":[\"bar\",\"baz\"]}","signature":"r7TvJ\/AuvAY7dKZ+7wQyI0PdyLivANZzPB35j8Xuyps="}';
        SecureLine::fromLine(\json_decode((string)$line, true), [
            'key' => 'cba',
        ]);
    }
}
