<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\CoversClass;

use Gbhorwood\Redactem\Redact;

#[CoversClass(\Gbhorwood\Redactem\Redact::class)]
#[UsesClass(\Gbhorwood\Redactem\Redact::class)]
class EmailTest extends TestCase
{
    /**
     * Match email addresses
     *
     * @dataProvider emailProvider
     */
    public function testMatchEmail($email)
    {
        $key = uniqid();
        $matchEmailFunction = Redact::matchEmailFunction();
        $this->assertTrue($matchEmailFunction($key, $email));
    }

    /**
     * Redact emails
     *
     * @dataProvider jsonProvider
     */
    public function testRedactEmail($testJson, $expectedJson)
    {
        $result = Redact::emails($testJson);
        $this->assertEquals($result, $expectedJson);
    }

    /**
     * Provide sample email addresses
     *
     * @return Array
     */
    public static function emailProvider():Array
    {
        return [
            ["x@example.ca"],
            ["gbhorwood@s.io"],
            ["gbhorwood@s.studio"],
            ["gbhorwood@example.ca"],
            ["gbhorwood@s.solutions"],
            ["very.common@example.ca"],
            ["/#!$%&'*+-/=?^_`{}|~@example.ca"],
            ["other.email-with-dash@example.ca"],
            ["fully-qualified-domain@example.ca"],
            ["example-indeed@strange-example.ca"],
            ["\"very.unusual.@.unusual.ca\"@example.ca"],
            ["disposable.style.email.with+symbol@example.ca"],
        ];
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
            ['{"foo":"bar","somemail":"gbhorwood@example.ca"}', '{"foo":"bar","somemail":"gb*****od@ex***le.ca"}'],
            // redaction on array elements
            ['{"foo":"bar","mails":["gbhorwood@example.ca","gbh@example.ca","gbh@fg.ca","\"very.unusual.@.unusual.ca\"@example.ca"]}', '{"foo":"bar","mails":["gb*****od@ex***le.ca","g**@ex***le.ca","g**@f*.ca","\"v***********************a\"@ex***le.ca"]}'],
            // redaction on string
            ['{"foo":"bar","somestring":"{\"foo\":\"bar\",\"somemail\":\"gbhorwood@example.ca\"}"}', '{"foo":"bar","somestring":"{\"foo\":\"bar\",\"somemail\":\"gb*****od@ex***le.ca\"}"}'],
        ];
    }
}
