<?php

namespace App\Support;

class RichTextSanitizer
{
    public static function sanitize(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $html = trim($html);
        if ($html === '') {
            return null;
        }

        $html = preg_replace('#<\s*(script|style)[^>]*>.*?<\s*/\s*\1\s*>#is', '', $html) ?? $html;

        $allowedTags = '<p><br><strong><b><em><i><u><s><ul><ol><li><a><blockquote><pre><code><h3><h4>';
        $clean = strip_tags($html, $allowedTags);

        $wrapped = '<div>' . $clean . '</div>';
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $allowedAttributes = [
            'a' => ['href', 'target', 'rel'],
        ];

        foreach ($dom->getElementsByTagName('*') as $node) {
            $tag = strtolower($node->nodeName);
            if (!isset($allowedAttributes[$tag])) {
                while ($node->attributes->length > 0) {
                    $node->removeAttributeNode($node->attributes->item(0));
                }
                continue;
            }

            $keep = $allowedAttributes[$tag];
            $toRemove = [];

            foreach ($node->attributes as $attr) {
                $name = strtolower($attr->name);
                if (!in_array($name, $keep, true)) {
                    $toRemove[] = $name;
                }
            }

            foreach ($toRemove as $name) {
                $node->removeAttribute($name);
            }

            if ($tag === 'a') {
                $href = $node->getAttribute('href');
                if ($href !== '' && !self::isSafeHref($href)) {
                    $node->removeAttribute('href');
                }

                $target = strtolower($node->getAttribute('target'));
                if ($target !== '' && !in_array($target, ['_blank', '_self'], true)) {
                    $node->removeAttribute('target');
                }

                if (strtolower($node->getAttribute('target')) === '_blank') {
                    $rel = strtolower(trim($node->getAttribute('rel')));
                    $rels = array_filter(explode(' ', $rel));
                    if (!in_array('noopener', $rels, true)) {
                        $rels[] = 'noopener';
                    }
                    if (!in_array('noreferrer', $rels, true)) {
                        $rels[] = 'noreferrer';
                    }
                    $node->setAttribute('rel', implode(' ', array_unique($rels)));
                }
            }
        }

        $result = $dom->saveHTML();
        $result = preg_replace('#^<div>|</div>$#', '', $result ?? '') ?? '';

        return trim($result) === '' ? null : $result;
    }

    public static function plainTextLength(?string $html): int
    {
        return mb_strlen(trim(strip_tags((string) $html)));
    }

    private static function isSafeHref(string $href): bool
    {
        $href = trim($href);
        if ($href === '') {
            return false;
        }

        if (str_starts_with($href, '#') || str_starts_with($href, '/')) {
            return true;
        }

        $scheme = parse_url($href, PHP_URL_SCHEME);
        if ($scheme === null) {
            return true;
        }

        $scheme = strtolower($scheme);
        return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
    }
}
