<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlSanitizer
{
    private static ?HTMLPurifier $purifier = null;

    /**
     * Sanitize rich-text HTML, keeping only the tags the editor is allowed to produce.
     */
    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        return self::purifier()->purify($html);
    }

    private static function purifier(): HTMLPurifier
    {
        if (self::$purifier === null) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.Allowed', 'h2,h3,p,strong,em,b,i,a[href|title],ul,ol,li,br');
            $config->set('AutoFormat.RemoveEmpty', true);
            $config->set('HTML.TargetBlank', true);
            // No writable definition cache on shared hosting.
            $config->set('Cache.DefinitionImpl', null);

            self::$purifier = new HTMLPurifier($config);
        }

        return self::$purifier;
    }
}
