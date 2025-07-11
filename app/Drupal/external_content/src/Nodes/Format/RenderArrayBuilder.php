<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Format;

use Drupal\Core\Render\Element\HtmlTag;
use Drupal\Core\Render\Markup;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\external_content\Contract\Builder\RenderArray\Builder;
use Drupal\external_content\Contract\Builder\RenderArray\ChildBuilder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\Node;

/**
 * @implements \Drupal\external_content\Contract\Builder\RenderArray\Builder<\Drupal\external_content\Nodes\Format\Format>
 */
final readonly class RenderArrayBuilder implements Builder, TrustedCallbackInterface {

  public function supports(Node $node): bool {
    return $node instanceof Format;
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): RenderArray {
    $element = new RenderArray([
      '#type' => 'html_tag',
      '#tag' => $node->format->toHtmlTag(),
      '#pre_render' => [
        HtmlTag::preRenderHtmlTag(...),
        self::preRenderTag(...),
      ],
    ]);
    $child_builder->buildChildren($node, $element);
    return $element;
  }

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
