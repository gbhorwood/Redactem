<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\CoversClass;

use Gbhorwood\Redactem\Redact;

#[CoversClass(\Gbhorwood\Redactem\Redact::class)]
#[UsesClass(\Gbhorwood\Redactem\Redact::class)]
class PasswordTest extends TestCase
{
    /**
     * Redact passwords
     *
     * @dataProvider jsonProvider
     */
    public function testRedactPassword($testJson, $expectedJson)
    {
        $result = Redact::passwords($testJson);
        $this->assertEquals($result, $expectedJson);
    }

    /**
     * Redact passwords with custom redaction text
     *
     * @dataProvider jsonProvider
     */
    public function testRedactPasswordCustomRedactionText($testJson, $expectedJson)
    {
        $customRedactionText = "REDACTED";
        $expectedJson = str_replace("*****", $customRedactionText, $expectedJson);
        $result = Redact::passwords($testJson, $customRedactionText);
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
            // redaction on password top level
            ['{"foo":"bar","password":"somepassword"}', '{"foo":"bar","password":"*****"}'],
            // redaction on password top level case-insensitive
            ['{"foo":"bar","PASSword":"somepassword"}', '{"foo":"bar","PASSword":"*****"}'],
            // redaction on pwd top level 
            ['{"foo":"bar","pwd":"somepassword"}', '{"foo":"bar","pwd":"*****"}'],
            // redaction on pwd top level case-insensitive
            ['{"foo":"bar","pwD":"somepassword"}', '{"foo":"bar","pwD":"*****"}'],
            // redaction on inner level
            ['{"foo":"bar","user":{"name":"jasvinder","password":"somepassword"}}', '{"foo":"bar","user":{"name":"jasvinder","password":"*****"}}'],
            // redaction on inner level
            ['{"foo":"bar","user":{"name":"jasvinder","passWORD":"somepassword"}}', '{"foo":"bar","user":{"name":"jasvinder","passWORD":"*****"}}'],
            // redaction on inner inner level
            ['{"foo":"bar","user":{"credentials":{"name":"jasvinder","password":"somepassword"}}}', '{"foo":"bar","user":{"credentials":{"name":"jasvinder","password":"*****"}}}'],
            // redaction on json as string
            ['{"foo":"bar","astring":"{\"name\":\"jasvinder\", \"password\":\"somepassword\"}"}', '{"foo":"bar","astring":"{\"name\":\"jasvinder\",\"password\":\"*****\"}"}'],
            // redaction on json as string inner level
            ['{"foo":"bar","user":{"astring":"{\"name\":\"jasvinder\", \"password\":\"somepassword\"}"}}', '{"foo":"bar","user":{"astring":"{\"name\":\"jasvinder\",\"password\":\"*****\"}"}}'],
            // redaction on each element of array
            ['{"foo":"bar","users":[{"name":"one","password":"somepassword"},{"name":"two","password":"somepassword"}]}', '{"foo":"bar","users":[{"name":"one","password":"*****"},{"name":"two","password":"*****"}]}'],
        ];
    }
}