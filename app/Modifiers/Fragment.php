<?php

namespace App\Modifiers;

use Illuminate\Support\Str;
use Statamic\Facades\Markdown;
use Statamic\Modifiers\Modifier;

class Fragment extends Modifier
{
    /**
     * Modify a value.
     *
     * @param mixed  $value    The value to be modified
     * @param array  $params   Any parameters used in the modifier
     * @param array  $context  Contextual values
     * @return mixed
     */
    public function index($value, $params, $context)
    {
        if (is_array($value)) {
            // Value is a Bard Field
            $html = collect($value)
                ->filter(fn($set) => $set['type'] === "text")
                ->implode('text', " ");
        } else {
            // Value is Markdown content
            $html = Markdown::parser('default')->parse($value);
        }

        // Remove HTML tags
        $content = trim( // 3. strip leading/trailing space
            preg_replace(
                '/ {2,}/', // 2. strip extra spaces
                ' ',
                preg_replace(
                    '/<[^>]*>/', // 1. strip_tags
                    ' ',
                    $html
                )
            )
        );

        // Return an Excerpt
        return Str::words(
            $content,
            array_get($params, 0, 16),
            array_get($params, 1, '…')
        );
    }
}
