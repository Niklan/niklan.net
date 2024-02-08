<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Renderer;

use Dflydev\DotAccessData\DataInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\Xml;
use League\CommonMark\Xml\XmlNodeRendererInterface;

/**
 * Provides custom fenced code renderer.
 *
 * @ingroup markdown
 */
final class FencedCodeRenderer implements NodeRendererInterface, XmlNodeRendererInterface {

  /**
   * {@inheritdoc}
   */
  public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable {
    \assert($node instanceof FencedCode);

    $attrs = $node->data->getData('attributes');
    $this->parseCodeInformation($node, $attrs);
    $content = Xml::escape($node->getLiteral());
    $code = new HtmlElement('code', [], $content);

    return new HtmlElement('pre', $attrs->export(), $code);
  }

  /**
   * {@inheritdoc}
   */
  public function getXmlTagName(Node $node): string {
    return 'code_block';
  }

  /**
   * {@inheritdoc}
   */
  public function getXmlAttributes(Node $node): array {
    \assert($node instanceof FencedCode);

    $info = $node->getInfo();

    if ($info === NULL || $info === '') {
      return [];
    }

    return ['info' => $info];
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

    if ($info_parts[1] !== '') {
      $attrs->set('data-language', \rtrim($info_parts[1]));
    }

    // The additional information expected as JSON after language.
    if (!\array_key_exists(2, $info_parts) || !\stristr($info_parts[2], '{')) {
      return;
    }

    $attrs->set('data-info', $info_parts[2]);
  }

}
