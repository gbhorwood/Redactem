<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\CoversClass;

use Gbhorwood\Redactem\Redact;

#[CoversClass(\Gbhorwood\Redactem\Redact::class)]
#[UsesClass(\Gbhorwood\Redactem\Redact::class)]
class ByRegexTest extends TestCase
{
    /**
     * Redact by regex
     *
     * @dataProvider jsonProvider
     */
    public function testRedactByRegex($testJson, $regex, $text, $expectedJson)
    {
        $result = Redact::byRegex($testJson, $regex, $text);
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
            ['{"sum":25,"lessthanj":"abcdefghi","morethanj":"klmnopq"}', '/^[a-j]*$/', null, '{"sum":25,"lessthanj":"*****","morethanj":"klmnopq"}'],
            ['{"sum":25,"anarray":{"lessthanj":"abcdefghi","morethanj":"klmnopq"}}', '/^[a-j]*$/', null, '{"sum":25,"anarray":{"lessthanj":"*****","morethanj":"klmnopq"}}'],
            ['{"sum":25,"astring":"{\"lessthanj\":\"abcdefghi\", \"morethanj\":\"klmnopq\"}"}', '/^[a-j]*$/', null, '{"sum":25,"astring":"{\"lessthanj\":\"*****\",\"morethanj\":\"klmnopq\"}"}'],
            ['{"sum":25,"anarray":{"astring":"{\"lessthanj\":\"abcdefghi\", \"morethanj\":\"klmnopq\"}"}}', '/^[a-j]*$/', null, '{"sum":25,"anarray":{"astring":"{\"lessthanj\":\"*****\",\"morethanj\":\"klmnopq\"}"}}'],

            ['{"sum":25,"lessthanj":"abcdefghi","morethanj":"klmnopq"}', '/^[a-j]*$/', 'REDACTED', '{"sum":25,"lessthanj":"REDACTED","morethanj":"klmnopq"}'],
            ['{"sum":25,"anarray":{"lessthanj":"abcdefghi","morethanj":"klmnopq"}}', '/^[a-j]*$/', 'REDACTED', '{"sum":25,"anarray":{"lessthanj":"REDACTED","morethanj":"klmnopq"}}'],
            ['{"sum":25,"astring":"{\"lessthanj\":\"abcdefghi\", \"morethanj\":\"klmnopq\"}"}', '/^[a-j]*$/', 'REDACTED', '{"sum":25,"astring":"{\"lessthanj\":\"REDACTED\",\"morethanj\":\"klmnopq\"}"}'],
            ['{"sum":25,"anarray":{"astring":"{\"lessthanj\":\"abcdefghi\", \"morethanj\":\"klmnopq\"}"}}', '/^[a-j]*$/', 'REDACTED', '{"sum":25,"anarray":{"astring":"{\"lessthanj\":\"REDACTED\",\"morethanj\":\"klmnopq\"}"}}'],
        ];
    }
}