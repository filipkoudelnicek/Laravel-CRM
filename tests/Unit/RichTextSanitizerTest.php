<?php

namespace Tests\Unit;

use App\Support\RichTextSanitizer;
use PHPUnit\Framework\TestCase;

class RichTextSanitizerTest extends TestCase
{
    public function test_plain_text_and_czech_characters_do_not_gain_xml_or_wrapper_markup(): void
    {
        $this->assertSame('Příští středu nasadíme změnu.', RichTextSanitizer::sanitize('Příští středu nasadíme změnu.'));
    }

    public function test_rich_text_keeps_formatting_and_removes_scripts_handlers_and_dangerous_links(): void
    {
        $clean = RichTextSanitizer::sanitize('<p onclick="alert(1)">Poznámka <strong>hotovo</strong></p><script>alert(2)</script><a href="java&#10;script:alert(3)" target="_blank">odkaz</a>');
        $this->assertStringContainsString('<strong>hotovo</strong>', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('script', $clean);
        $this->assertStringNotContainsString('href=', $clean);
        $this->assertStringContainsString('noopener', $clean);
    }
}
