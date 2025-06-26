<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\RemoteVideo;

use Drupal\external_content\Contract\Importer\Html\Parser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final readonly class HtmlParser implements Parser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }

    if ($request->currentHtmlNode->getAttribute('data-selector') !== 'niklan:leaf-directive') {
      return FALSE;
    }

    if ($request->currentHtmlNode->getAttribute('data-type') !== 'youtube') {
      return FALSE;
    }

    return $request->currentHtmlNode->hasAttribute('data-vid');
  }

  public function parse(HtmlParseRequest $request): Content {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $video_id = $request->currentHtmlNode->getAttribute('data-vid');

    return new RemoteVideo("https://youtu.be/{$video_id}");
  }

}
