<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

final class HtmlSanitizer
{
    private const ALLOWED_ELEMENTS = [
        'a', 'blockquote', 'br', 'code', 'del', 'div', 'em', 'figcaption', 'figure',
        'h2', 'h3', 'h4', 'h5', 'hr', 'img', 'li', 'ol', 'p', 'pre', 'span',
        'strong', 'sub', 'sup', 'table', 'tbody', 'td', 'th', 'thead', 'tr', 'u', 'ul',
    ];

    private const GLOBAL_ATTRIBUTES = ['class', 'title'];

    private const ELEMENT_ATTRIBUTES = [
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'width', 'height', 'loading'],
        'td' => ['colspan', 'rowspan'],
        'th' => ['colspan', 'rowspan', 'scope'],
    ];

    public static function sanitize(?string $html): string
    {
        if (! filled($html)) {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="kba-sanitizer-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('kba-sanitizer-root');
        if (! $root) {
            return e(strip_tags($html));
        }

        self::cleanChildren($root);

        $safe = '';
        foreach ($root->childNodes as $child) {
            $safe .= $document->saveHTML($child);
        }

        return $safe;
    }

    private static function cleanChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);
            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button'], true)) {
                $node->parentNode?->removeChild($node);
                continue;
            }

            if (! in_array($tag, self::ALLOWED_ELEMENTS, true)) {
                self::cleanChildren($node);
                while ($node->firstChild) {
                    $node->parentNode?->insertBefore($node->firstChild, $node);
                }
                $node->parentNode?->removeChild($node);
                continue;
            }

            self::cleanAttributes($node, $tag);
            self::cleanChildren($node);
        }
    }

    private static function cleanAttributes(DOMElement $element, string $tag): void
    {
        $allowed = array_merge(self::GLOBAL_ATTRIBUTES, self::ELEMENT_ATTRIBUTES[$tag] ?? []);

        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->name);
            if (! in_array($name, $allowed, true) || str_starts_with($name, 'on')) {
                $element->removeAttribute($attribute->name);
            }
        }

        foreach (['href', 'src'] as $urlAttribute) {
            if (! $element->hasAttribute($urlAttribute)) {
                continue;
            }

            $value = trim($element->getAttribute($urlAttribute));
            if (! preg_match('~^(https?://|mailto:|tel:|/|#)~i', $value)) {
                $element->removeAttribute($urlAttribute);
            }
        }

        if ($tag === 'a' && $element->getAttribute('target') === '_blank') {
            $element->setAttribute('rel', 'noopener noreferrer');
        }

        if ($tag === 'img') {
            $element->setAttribute('loading', 'lazy');
        }
    }
}
