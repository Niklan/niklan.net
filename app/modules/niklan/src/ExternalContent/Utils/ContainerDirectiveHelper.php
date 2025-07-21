<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Utils;

use Symfony\Component\DomCrawler\Crawler;

final readonly class ContainerDirectiveHelper {

  public static function findDomInlineContent(\DOMNode $node): ?\DOMNode {
    // Since container directive can be nested within each other, there is
    // a possibility that a parent directive may not have an inline-content
    // while a nested one does. Therefore, it is crucial that the inline-content
    // is searched only among direct child elements and not throughout the
    // entire child tree.
    return (new Crawler($node))->filterXPath('.//*[1]/div[@data-selector="inline-content"]')->getNode(0);
  }

  public static function findDomContent(\DOMNode $node): ?\DOMNode {
    // @see ::findDomInlineContent() for explanation.
    return (new Crawler($node))->filterXPath('.//*[1]/div[@data-selector="content"]')->getNode(0);
  }

}
