<?php

declare(strict_types=1);

namespace Drupal\external_content\Builder;

use Drupal\Core\Render\Element\HtmlTag;
use Drupal\Core\Render\Markup;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Utils\RenderArrayBuilderHelper;

/**
 * Provides a simple HTML render array builder.
 */
final class ElementRenderArrayBuilder implements RenderArrayBuilderInterface, TrustedCallbackInterface {

  #[\Override]
  public function build(NodeInterface $node, ChildRenderArrayBuilderInterface $child_builder): RenderArrayBuilderResult {
    \assert($node instanceof Element);

    return RenderArrayBuilderResult::withRenderArray([
      '#type' => 'html_tag',
      '#tag' => $node->getTag(),
      '#attributes' => $node->getAttributes()->all(),
      '#pre_render' => [
        HtmlTag::preRenderHtmlTag(...),
        self::preRenderTag(...),
      ],
      'children' => RenderArrayBuilderHelper::buildChildren(
        node: $node,
        child_builder: $child_builder,
      )->result(),
    ]);
  }

  #[\Override]
  public function supportsBuild(NodeInterface $node): bool {
    return $node instanceof Element;
  }

  /**
   * Removes unwanted 'html_tag' newline character.
   *
   * Without this workaround some text can become weirdly rendered with an
   * additional space after element.
   *
   * Example:
   *
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

  #[\Override]
  public static function trustedCallbacks(): array {
    return [
      'preRenderTag',
    ];
  }

}
