<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Transformer;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Transformer\Html\HtmlImporterContext;
use Drupal\niklan\ExternalContent\Node\RemoteVideoNode;

final readonly class RemoteVideoHtmlNodeParser implements HtmlNodeParser {

  public function supports(\DOMNode $node, HtmlImporterContext $context): bool {
    if (!$node instanceof \DOMElement) {
      return FALSE;
    }

    if (!$node->hasAttribute('data-selector') || $node->getAttribute('data-selector') !== 'niklan:leaf-directive') {
      return FALSE;
    }

    if ($node->getAttribute('data-type') !== 'youtube') {
      return FALSE;
    }

    return $node->hasAttribute('data-vid');
  }

  public function parse(\DOMNode $node, HtmlImporterContext $context): ContentNode {
    \assert($node instanceof \DOMElement);
    $video_id = $node->getAttribute('data-vid');

    return new RemoteVideoNode("https://youtu.be/{$video_id}");
  }

}
