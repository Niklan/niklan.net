<?php

declare(strict_types=1);

namespace Drupal\external_content\Utils;

use Drupal\Core\Render\Markup;
use Drupal\Core\Security\TrustedCallbackInterface;

final readonly class HtmlTagHelper implements TrustedCallbackInterface {

  /**
   * Removes unwanted 'html_tag' newline character.
   *
   * Without this workaround some text can become weirdly rendered with an
   * additional space after element.
   *
   * Example:
   * @code
   *  Hello, <a href="#">World</a> !
   *                              ^
   *                              This will be removed by this fix.
   * @endcode
   *
   * @see https://www.drupal.org/project/drupal/issues/1268180
   */
  public static function preRenderTag(array $element): array {
    $inline_tags = [
      'a',
      'abbr',
      'acronym',
      'b',
      'bdo',
      'big',
      'br',
      'button',
      'cite',
      'code',
      'dfn',
      'em',
      'i',
      'img',
      'input',
      'kbd',
      'label',
      'map',
      'object',
      'output',
      'q',
      'samp',
      'script',
      'select',
      'small',
      'span',
      'strong',
      'sub',
      'sup',
      'textarea',
      'time',
      'tt',
      'var',
    ];

    if (!\in_array($element['#tag'], $inline_tags)) {
      return $element;
    }

    $suffix = $element['#suffix'];
    \assert($suffix instanceof Markup);
    $element['#suffix'] = Markup::create(\rtrim((string) $suffix));

    return $element;
  }

  public static function trustedCallbacks(): array {
    return [
      'preRenderTag',
    ];
  }

}
