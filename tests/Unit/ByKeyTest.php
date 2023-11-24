<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\CoversClass;

use Gbhorwood\Redactem\Redact;

#[CoversClass(\Gbhorwood\Redactem\Redact::class)]
#[UsesClass(\Gbhorwood\Redactem\Redact::class)]
class ByKeyTest extends TestCase
{
    /**
     * Redact by key
     *
     * @dataProvider jsonProvider
     */
    public function testRedactByKey($testJson, $key, $case, $text, $expectedJson)
    {
        $result = Redact::byKey($testJson, $key, $case, $text);
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
            [99, '', false, null, 99],
            [null, '', false, null, null],
            ['notjson','', false, null, 'notjson'],
            // no redaction
            ['{"foo":"bar"}','', false, null, '{"foo":"bar"}'],
            // redact by key default
            ['{"clown":"foo"}', 'clown', false, null, '{"clown":"*****"}'],
            ['{"clown":"bar","baz":"quux"}', 'clown', false, null, '{"clown":"*****","baz":"quux"}'],
            ['{"clown":"bar","Clown":"bar"}', 'clown', false, null, '{"clown":"*****","Clown":"*****"}'],
            ['{"somearray":{"clown":"bar","notclown":"bar"},"baz":"quux"}', 'clown', false, null, '{"somearray":{"clown":"*****","notclown":"bar"},"baz":"quux"}'],
            ['{"somearray":{"cLOwn":"bar","notclown":"bar"},"baz":"quux"}', 'clown', false, null, '{"somearray":{"cLOwn":"*****","notclown":"bar"},"baz":"quux"}'],
            ['{"somestring":"{\"foo\":\"bar\", \"clown\":\"bar\", \"cLOwN\": \"bar\"}"}', 'clown', false, null, '{"somestring":"{\"foo\":\"bar\",\"clown\":\"*****\",\"cLOwN\":\"*****\"}"}'],
            ['{"somestring":{"otherstring":"{\"foo\":\"bar\", \"clown\":\"bar\", \"cLOwN\": \"bar\"}"}}', 'clown', false, null, '{"somestring":{"otherstring":"{\"foo\":\"bar\",\"clown\":\"*****\",\"cLOwN\":\"*****\"}"}}'],
            // redact by key case sensitive
            ['{"clown":"foo"}', 'clown', true, null, '{"clown":"*****"}'],
            ['{"clown":"bar","baz":"quux"}', 'clown', true, null, '{"clown":"*****","baz":"quux"}'],
            ['{"clown":"bar","Clown":"bar"}', 'clown', true, null, '{"clown":"*****","Clown":"bar"}'],
            ['{"somearray":{"clown":"bar","notclown":"bar"},"baz":"quux"}', 'clown', true, null, '{"somearray":{"clown":"*****","notclown":"bar"},"baz":"quux"}'],
            ['{"somearray":{"cLOwn":"bar","notclown":"bar"},"baz":"quux"}', 'clown', true, null, '{"somearray":{"cLOwn":"bar","notclown":"bar"},"baz":"quux"}'],
            ['{"somestring":"{\"foo\":\"bar\", \"clown\":\"bar\", \"cLOwN\": \"bar\"}"}', 'clown', true, null, '{"somestring":"{\"foo\":\"bar\",\"clown\":\"*****\",\"cLOwN\":\"bar\"}"}'],
            ['{"somestring":{"otherstring":"{\"foo\":\"bar\", \"clown\":\"bar\", \"cLOwN\": \"bar\"}"}}', 'clown', true, null, '{"somestring":{"otherstring":"{\"foo\":\"bar\",\"clown\":\"*****\",\"cLOwN\":\"bar\"}"}}'],
            // redact by key with custom redaction text
            ['{"clown":"foo"}', 'clown', true, 'REDACTED', '{"clown":"REDACTED"}'],
            ['{"clown":"bar","baz":"quux"}', 'clown', true, 'REDACTED', '{"clown":"REDACTED","baz":"quux"}'],
            ['{"clown":"bar","Clown":"bar"}', 'clown', true, 'REDACTED', '{"clown":"REDACTED","Clown":"bar"}'],
            ['{"somearray":{"clown":"bar","notclown":"bar"},"baz":"quux"}', 'clown', true, 'REDACTED', '{"somearray":{"clown":"REDACTED","notclown":"bar"},"baz":"quux"}'],
            ['{"somearray":{"cLOwn":"bar","notclown":"bar"},"baz":"quux"}', 'clown', true, 'REDACTED', '{"somearray":{"cLOwn":"bar","notclown":"bar"},"baz":"quux"}'],
            ['{"somestring":"{\"foo\":\"bar\", \"clown\":\"bar\", \"cLOwN\": \"bar\"}"}', 'clown', true, 'REDACTED', '{"somestring":"{\"foo\":\"bar\",\"clown\":\"REDACTED\",\"cLOwN\":\"bar\"}"}'],
            ['{"somestring":{"otherstring":"{\"foo\":\"bar\", \"clown\":\"bar\", \"cLOwN\": \"bar\"}"}}', 'clown', true, 'REDACTED', '{"somestring":{"otherstring":"{\"foo\":\"bar\",\"clown\":\"REDACTED\",\"cLOwN\":\"bar\"}"}}'],
        ];
    }
}