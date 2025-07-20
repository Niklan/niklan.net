<?php

declare(strict_types=1);

namespace Drupal\niklan\Markup\Markdown\Renderer;

use Dflydev\DotAccessData\DataInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\Xml;

/**
 * @ingroup markdown
 */
final class FencedCodeRenderer implements NodeRendererInterface {

  #[\Override]
  public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable {
    \assert($node instanceof FencedCode);

    $attrs = $node->data->getData('attributes');
    $this->parseCodeInformation($node, $attrs);
    $content = Xml::escape($node->getLiteral());
    $code = new HtmlElement('code', [], $content);
    /** @var array<string> $attributes */
    $attributes = $attrs->export();

    return new HtmlElement('pre', $attributes, $code);
  }

  /**
   * Parses additional code information.
   */
  protected function parseCodeInformation(FencedCode $node, DataInterface $attrs): void {
    // Info comes right after fence.
    // @code
    // ```php foo
    //    ^^^^^^^
    // @endcode
    $info_parts = [];
    \preg_match('/^(.+)\s?({.+})/', $node->getInfo() ?? '', $info_parts);

    if (\count($info_parts) === 0) {
      $attrs->set('data-language', $node->getInfo());

      return;
    }

    $attrs->set('data-language', \rtrim($info_parts[1]));
    // The additional information expected as JSON after language.
    if (!\array_key_exists(2, $info_parts) || !\stristr($info_parts[2], '{')) {
      return;
    }

    $attrs->set('data-info', $info_parts[2]);
  }

}
