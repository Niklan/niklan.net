<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Parser\ChildHtmlParserInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Node\NodeList;

final class ChildHtmlParser implements ChildHtmlParserInterface {

  private EnvironmentInterface $environment;

  #[\Override]
  public function parse(\DOMNodeList $children): NodeList {
    $list = new NodeList();

    foreach ($children as $child) {
      \assert($child instanceof \DOMNode);
      $this->parseChild($child, $list);
    }

    return $list;
  }

  #[\Override]
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

  private function parseChild(\DOMNode $child, NodeList $list): void {
    foreach ($this->environment->getHtmlParsers() as $parser) {
      \assert($parser instanceof HtmlParserInterface);
      $result = $parser->parseNode($child, $this);

      if ($result->hasReplacement()) {
        // @todo Remove when resolved: https://github.com/phpstan/phpstan/issues/12495
        \assert($result->replacement() instanceof NodeInterface);
        $list->addChild($result->replacement());
      }

      if ($result->shouldNotContinue()) {
        break;
      }
    }
  }

}
