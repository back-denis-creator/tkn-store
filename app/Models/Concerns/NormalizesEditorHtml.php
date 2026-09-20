<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * The admin's Quill editor exports its HTML with every single space written
 * as a non-breaking one. A paragraph then holds no place a browser may break
 * at, so it runs off the side of a phone screen and widens the whole page.
 */
trait NormalizesEditorHtml
{
    /**
     * Cleans one text field both on the way in, so the editor cannot store
     * the non-breaking spaces again, and on the way out, so text saved
     * before this still wraps.
     */
    protected function editorHtml(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->withBreakableSpaces($value),
            set: fn (?string $value) => $this->withBreakableSpaces($value),
        );
    }

    private function withBreakableSpaces(?string $value): ?string
    {
        return $value === null ? null : str_replace(["\u{00A0}", '&nbsp;'], ' ', $value);
    }
}
