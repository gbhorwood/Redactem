<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\CoversClass;

use Gbhorwood\Redactem\Redact;

#[CoversClass(\Gbhorwood\Redactem\Redact::class)]
#[UsesClass(\Gbhorwood\Redactem\Redact::class)]
class ByKeysTest extends TestCase
{
    /**
     * Redact by key
     *
     * @dataProvider jsonProvider
     */
    public function testRedactByKeys($testJson, $key, $case, $text, $expectedJson)
    {
        $result = Redact::byKeys($testJson, $key, $case, $text);
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
            [99, [], false, null, 99],
            [null, [], false, null, null],
            ['notjson',[], false, null, 'notjson'],
            // no redaction
            ['{"foo":"bar"}',[], false, null, '{"foo":"bar"}'],
            // redact by key default
            ['{"height":25,"width":"wide"}', ['height', 'width'], false, null, '{"height":"*****","width":"*****"}'],
            ['{"foo":"bar","height":25,"width":"wide"}', ['height', 'width'], false, null, '{"foo":"bar","height":"*****","width":"*****"}'],
            ['{"foo":"bar","Height":25,"widTH":"wide"}', ['height', 'width'], false, null, '{"foo":"bar","Height":"*****","widTH":"*****"}'],
            ['{"foo":"bar","size":{"height":25,"width":"wide"}}', ['height', 'width'], false, null, '{"foo":"bar","size":{"height":"*****","width":"*****"}}'],
            ['{"foo":"bar","size":{"height":25,"width":"wide"},"width":"alsowide"}', ['height', 'width'], false, null, '{"foo":"bar","size":{"height":"*****","width":"*****"},"width":"*****"}'],
            ['{"foo":"bar","size":{"Height":25,"widTH":"wide"}}', ['height', 'width'], false, null, '{"foo":"bar","size":{"Height":"*****","widTH":"*****"}}'],
            ['{"foo":"bar","size":{"Height":25,"widTH":"wide"},"width":"alsowide"}', ['height', 'width'], false, null, '{"foo":"bar","size":{"Height":"*****","widTH":"*****"},"width":"*****"}'],
            ['{"foo":"bar","astring":"{\"height\":\"25\", \"width\":\"wide\"}"}', ['height', 'width'], false, null, '{"foo":"bar","astring":"{\"height\":\"*****\",\"width\":\"*****\"}"}'],
            ['{"foo":"bar","size":{"astring":"{\"height\":25, \"width\":\"wide\"}"}}', ['height', 'width'], false, null, '{"foo":"bar","size":{"astring":"{\"height\":\"*****\",\"width\":\"*****\"}"}}'],
            // redact by key case sensitive
            ['{"height":25,"width":"wide"}', ['height', 'width'], true, null, '{"height":"*****","width":"*****"}'],
            ['{"foo":"bar","height":25,"width":"wide"}', ['height', 'width'], true, null, '{"foo":"bar","height":"*****","width":"*****"}'],
            ['{"foo":"bar","Height":25,"widTH":"wide"}', ['height', 'width'], true, null, '{"foo":"bar","Height":25,"widTH":"wide"}'],
            ['{"foo":"bar","size":{"height":25,"width":"wide"}}', ['height', 'width'], true, null, '{"foo":"bar","size":{"height":"*****","width":"*****"}}'],
            ['{"foo":"bar","size":{"height":25,"width":"wide"},"width":"alsowide"}', ['height', 'width'], true, null, '{"foo":"bar","size":{"height":"*****","width":"*****"},"width":"*****"}'],
            ['{"foo":"bar","size":{"Height":25,"widTH":"wide"}}', ['height', 'width'], true, null, '{"foo":"bar","size":{"Height":25,"widTH":"wide"}}'],
            ['{"foo":"bar","size":{"Height":25,"widTH":"wide"},"width":"alsowide"}', ['height', 'width'], true, null, '{"foo":"bar","size":{"Height":25,"widTH":"wide"},"width":"*****"}'],
            ['{"foo":"bar","astring":"{\"height\":\"25\", \"width\":\"wide\"}"}', ['height', 'width'], true, null, '{"foo":"bar","astring":"{\"height\":\"*****\",\"width\":\"*****\"}"}'],
            ['{"foo":"bar","size":{"astring":"{\"height\":25, \"width\":\"wide\"}"}}', ['height', 'width'], true, null, '{"foo":"bar","size":{"astring":"{\"height\":\"*****\",\"width\":\"*****\"}"}}'],
            // redact by key with custom redaction text
            ['{"height":25,"width":"wide"}', ['height', 'width'], true, 'REDACTED', '{"height":"REDACTED","width":"REDACTED"}'],
            ['{"foo":"bar","height":25,"width":"wide"}', ['height', 'width'], true, 'REDACTED', '{"foo":"bar","height":"REDACTED","width":"REDACTED"}'],
            ['{"foo":"bar","Height":25,"widTH":"wide"}', ['height', 'width'], true, 'REDACTED', '{"foo":"bar","Height":25,"widTH":"wide"}'],
            ['{"foo":"bar","size":{"height":25,"width":"wide"}}', ['height', 'width'], true, 'REDACTED', '{"foo":"bar","size":{"height":"REDACTED","width":"REDACTED"}}'],
            ['{"foo":"bar","size":{"height":25,"width":"wide"},"width":"alsowide"}', ['height', 'width'], true, 'REDACTED', '{"foo":"bar","size":{"height":"REDACTED","width":"REDACTED"},"width":"REDACTED"}'],
            ['{"foo":"bar","size":{"Height":25,"widTH":"wide"}}', ['height', 'width'], true, 'REDACTED', '{"foo":"bar","size":{"Height":25,"widTH":"wide"}}'],
            ['{"foo":"bar","size":{"Height":25,"widTH":"wide"},"width":"alsowide"}', ['height', 'width'], true, 'REDACTED', '{"foo":"bar","size":{"Height":25,"widTH":"wide"},"width":"REDACTED"}'],
            ['{"foo":"bar","astring":"{\"height\":\"25\", \"width\":\"wide\"}"}', ['height', 'width'], true, 'REDACTED', '{"foo":"bar","astring":"{\"height\":\"REDACTED\",\"width\":\"REDACTED\"}"}'],
            ['{"foo":"bar","size":{"astring":"{\"height\":25, \"width\":\"wide\"}"}}', ['height', 'width'], true, 'REDACTED', '{"foo":"bar","size":{"astring":"{\"height\":\"REDACTED\",\"width\":\"REDACTED\"}"}}'],
        ];
    }
}