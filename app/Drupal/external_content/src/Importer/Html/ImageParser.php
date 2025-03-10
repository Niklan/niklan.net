<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\ImageNode;

final class ImageParser implements HtmlNodeParser {

  public function supports(\DOMNode $node, HtmlImporterContext $context): bool {
    return $node instanceof \DOMElement && $node->nodeName === 'img';
  }

  public function parse(\DOMNode $node, HtmlImporterContext $context): ContentNode {
    \assert($node instanceof \DOMElement);

    return new ImageNode($node->getAttribute('src'), $node->getAttribute('alt'));
  }

}
