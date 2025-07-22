<?php

declare(strict_types=1);

namespace Drupal\external_content\Domain;

/**
 * @deprecated Not needed after Heading node is removed.
 */
enum HeadingTagType: string {

  case H1 = 'h1';
  case H2 = 'h2';
  case H3 = 'h3';
  case H4 = 'h4';
  case H5 = 'h5';
  case H6 = 'h6';

  public static function fromHtmlTag(string $tag): self {
    return match ($tag) {
      default => throw new \InvalidArgumentException("{$tag} is unsupported by HeadingTagType."),
      'h1' => self::H1,
      'h2' => self::H2,
      'h3' => self::H3,
      'h4' => self::H4,
      'h5' => self::H5,
      'h6' => self::H6,
    };
  }

}
