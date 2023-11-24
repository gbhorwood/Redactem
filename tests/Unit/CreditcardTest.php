<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\CoversClass;

use Gbhorwood\Redactem\Redact;

#[CoversClass(\Gbhorwood\Redactem\Redact::class)]
#[UsesClass(\Gbhorwood\Redactem\Redact::class)]
class CreditcardTest extends TestCase
{
    /**
     * Match credit cards
     *
     * @dataProvider creditcardProvider
     */
    public function testMatchCreditcard($cc)
    {
        $key = uniqid();
        $matchCreditcardFunction = Redact::matchCreditcardFunction();
        $this->assertTrue($matchCreditcardFunction($key, $cc));
    }

    /**
     * Redact credit cards
     *
     * @dataProvider jsonProvider
     */
    public function testRedactCreditcard($testJson, $expectedJson)
    {
        $result = Redact::creditcards($testJson);
        $this->assertEquals($result, $expectedJson);
    }

    /**
     * Provide input and expected json
     *
     * @return Array
     */
    public static function jsonProvider():Array
    {
        return [
            // not json
            [99, 99],
            [null, null],
            ['notjson', 'notjson'],
            // no redaction
            ['{"foo":"bar"}', '{"foo":"bar"}'],
            // redaction on top level
            ['{"foo":"bar","iscc":"4111111111111111"}', '{"foo":"bar","iscc":"****************"}'],
            // redaction on inner
            ['{"foo":"bar","anarray":{"iscc":"4111111111111111"}}', '{"foo":"bar","anarray":{"iscc":"****************"}}'],
            // redaction on top level string
            ['{"foo":"bar","jsonstring":"{\"foo\":\"bar\",\"iscc\":\"4111111111111111\"}"}', '{"foo":"bar","jsonstring":"{\"foo\":\"bar\",\"iscc\":\"****************\"}"}'],
            // redaction on inner string
            ['{"foo":"bar","isarray":{"jsonstring":"{\"foo\":\"bar\",\"iscc\":\"4111111111111111\"}"}}', '{"foo":"bar","isarray":{"jsonstring":"{\"foo\":\"bar\",\"iscc\":\"****************\"}"}}'],
        ];
    }


    /**
     * Provide sample credit cards
     *
     * @return Array
     */
    public static function creditcardProvider():Array
    {
        return [
            ["2222420000001113"],
            ["2223000048410010"],
            ["3530111333300000"],
            ["3530111333300000"],
            ["3566000020000410"],
            ["3566002020360505"],
            ["4001919257537193"],
            ["4007702835532454"],
            ["4012888888881881"],
            ["4111111111111111"],
            ["4263982640269299"],
            ["4263982640269299"],
            ["4263982640269299"],
            ["4917484589897107"],
            ["5105105105105100"],
            ["5200533989557118"],
            ["5425233430109903"],
            ["5425233430109903"],
            ["5555555555554444"],
            ["6011000990139424"],
            ["6011000991300009"],
            ["6011111111111117"],
            ["371449635398431"],
            ["374245455400126"],
            ["378282246310005"],
            ["378282246310005"],
            ["378734493671000"],
            ["30569309025904"],
            ["38520000023237"],
            ["4222222222222"],
            ["2222 4200 0000 1113"],
            ["3530 1113 3330 0000"],
            ["4001 9192 5753 7193"],
            ["4263 9826 4026 9299"],
            ["5105 1051 0510 5100"],
            ["5200 5339 8955 7118"],
            ["5425 2334 3010 9903"],
            ["6011 0009 9013 9424"],
            ["6011 1111 1111 1117"],
            ["2222-4200-0000-1113"],
            ["3530-1113-3330-0000"],
            ["4001-9192-5753-7193"],
            ["4263-9826-4026-9299"],
            ["5105-1051-0510-5100"],
            ["5200-5339-8955-7118"],
            ["5425-2334-3010-9903"],
            ["6011-0009-9013-9424"],
            ["6011-1111-1111-1117"],
        ];
    }
}