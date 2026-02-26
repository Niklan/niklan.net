<?php

declare(strict_types=1);

namespace Drupal\external_content\Utils;

final readonly class HtmlDomHelper {

  public static function parseAttributes(\DOMNode $node): array {
    $attributes = [];
    if ($node->hasAttributes()) {
      foreach ($node->attributes as $attribute) {
        $attributes[$attribute->name] = $attribute->value;
      }
    }
    return $attributes;
  }

}
