<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Importer\Html;

use Drupal\external_content\Importer\Html\Parser\HtmlParseRequest;
use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\ContentNode;
use Drupal\niklan\ExternalContent\Node\RemoteVideoNode;

final readonly class RemoteVideoParser implements HtmlNodeParser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }

    if (!$request->currentHtmlNode->hasAttribute('data-selector') || $request->currentHtmlNode->getAttribute('data-selector') !== 'niklan:leaf-directive') {
      return FALSE;
    }

    if ($request->currentHtmlNode->getAttribute('data-type') !== 'youtube') {
      return FALSE;
    }

    return $request->currentHtmlNode->hasAttribute('data-vid');
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $video_id = $request->currentHtmlNode->getAttribute('data-vid');

    return new RemoteVideoNode("https://youtu.be/{$video_id}");
  }

}
