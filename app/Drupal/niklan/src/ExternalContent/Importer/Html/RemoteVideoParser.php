<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Importer\Html\HtmlParserRequest;
use Drupal\external_content\Node\ContentNode;
use Drupal\niklan\ExternalContent\Node\RemoteVideoNode;

final readonly class RemoteVideoParser implements HtmlNodeParser {

  public function supports(HtmlParserRequest $request): bool {
    if (!$request->htmlNode instanceof \DOMElement) {
      return FALSE;
    }

    if (!$request->htmlNode->hasAttribute('data-selector') || $request->htmlNode->getAttribute('data-selector') !== 'niklan:leaf-directive') {
      return FALSE;
    }

    if ($request->htmlNode->getAttribute('data-type') !== 'youtube') {
      return FALSE;
    }

    return $request->htmlNode->hasAttribute('data-vid');
  }

  public function parse(HtmlParserRequest $request): ContentNode {
    \assert($request->htmlNode instanceof \DOMElement);
    $video_id = $request->htmlNode->getAttribute('data-vid');

    return new RemoteVideoNode("https://youtu.be/{$video_id}");
  }

}
