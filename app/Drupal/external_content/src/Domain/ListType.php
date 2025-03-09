<?php

declare(strict_types=1);

namespace Drupal\external_content\Domain;

enum ListType: string {

  case Number = 'number';
  case Bullet = 'bullet';

  public function toHtmlTag(): string {
    return match ($this) {
      self::Number => 'ol',
      self::Bullet => 'ul',
    };
  }

}
