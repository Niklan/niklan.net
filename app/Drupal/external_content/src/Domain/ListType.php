<?php

declare(strict_types=1);

namespace Drupal\external_content\Domain;

enum ListType: string {

  case Number = 'number';
  case Bullet = 'bullet';

  public static function fromHtmlTag(string $tag): self {
    return match ($tag) {
      default => throw new \InvalidArgumentException("{$tag} is unsupported by ListType."),
      'ul' => self::Bullet,
      'ol' => self::Number,
    };
  }

  public function toHtmlTag(): string {
    return match ($this) {
      self::Number => 'ol',
      self::Bullet => 'ul',
    };
  }

}
