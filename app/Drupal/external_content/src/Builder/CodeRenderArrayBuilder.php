<?php

declare(strict_types=1);

namespace Drupal\external_content\Builder;

use Drupal\Core\Render\Element\HtmlTag;
use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;
use Drupal\external_content\Node\Code;

/**
 * Provides a simple <code> render array builder.
 */
final class CodeRenderArrayBuilder implements RenderArrayBuilderInterface {

  #[\Override]
  public function build(NodeInterface $node, ChildRenderArrayBuilderInterface $child_builder): RenderArrayBuilderResult {
    \assert($node instanceof Code);

    return RenderArrayBuilderResult::withRenderArray([
      '#type' => 'html_tag',
      '#tag' => 'code',
      '#value' => \htmlentities($node->getLiteral()),
      // Make sure that the processing is consistent with the element builder.
      '#pre_render' => [
        HtmlTag::preRenderHtmlTag(...),
        ElementRenderArrayBuilder::preRenderTag(...),
      ],
    ]);
  }

  #[\Override]
  public function supportsBuild(NodeInterface $node): bool {
    return $node instanceof Code;
  }

}
