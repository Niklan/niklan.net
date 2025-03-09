<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Transformer;

use Drupal\external_content\Contract\Transformer\HtmlNodeTransformer;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Transformer\Html\HtmlTransformerContext;
use Drupal\niklan\ExternalContent\Node\RemoteVideoNode;

final readonly class RemoteVideoHtmlNodeTransformer implements HtmlNodeTransformer {

  public function supports(\DOMNode $node, HtmlTransformerContext $context): bool {
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

  public function transform(\DOMNode $node, HtmlTransformerContext $context): ContentNode {
    \assert($node instanceof \DOMElement);
    $video_id = $node->getAttribute('data-vid');

    return new RemoteVideoNode("https://youtu.be/{$video_id}");
  }

}
