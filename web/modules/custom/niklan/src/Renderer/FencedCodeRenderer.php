<?php declare(strict_types = 1);

namespace Drupal\niklan\Renderer;

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
    // Info words comes right after fence.
    // @code
    // ```php foo
    //    ^^^^^^^
    // @endcode
    $info_words = $node->getInfoWords();

    if (\count($info_words)) {
      if ($info_words[0] !== '') {
        $attrs->append('class', "language-{$info_words[0]}");
      }

      // The additional information expected as JSON after language.
      if (\array_key_exists(1, $info_words) && \stristr($info_words[1], '{')) {
        $attrs->set('data-info', $info_words[1]);
      }
    }

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

}
