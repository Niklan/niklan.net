<?php

declare(strict_types=1);

namespace Drupal\external_content\Domain;

enum TextFormatType: string {

  case Bold = 'bold';
  case Underline = 'underline';
  case Strikethrough = 'strikethrough';
  case Italic = 'italic';
  case Highlight = 'highlight';
  case Code = 'code';
  case Subscript = 'subscript';
  case Superscript = 'superscript';

  public function toHtmlTag(): string {
    return match ($this) {
      self::Bold => 'strong',
      self::Italic => 'em',
      self::Underline => 'u',
      self::Strikethrough => 's',
      self::Highlight => 'mark',
      self::Code => 'code',
      self::Subscript => 'sub',
      self::Superscript => 'sup',
    };
  }

}
