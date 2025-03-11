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
  case Deleted = 'deleted';
  case Inserted = 'inserted';

  public static function fromHtmlTag(string $tag): self {
    return match ($tag) {
      default => throw new \InvalidArgumentException("{$tag} is unsupported by TextFormatType."),
      'b', 'strong' => self::Bold,
      'u' => self::Underline,
      's' => self::Strikethrough,
      'i', 'em' => self::Italic,
      'mark' => self::Highlight,
      'code' => self::Code,
      'sub' => self::Subscript,
      'sup' => self::Superscript,
      'del' => self::Deleted,
      'ins' => self::Inserted,
    };
  }

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
      self::Deleted => 'del',
      self::Inserted => 'ins',
    };
  }

}
