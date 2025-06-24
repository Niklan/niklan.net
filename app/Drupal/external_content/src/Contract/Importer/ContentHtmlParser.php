<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer;

use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\ContentNode;

interface ContentHtmlParser {

  public function supports(HtmlParseRequest $request): bool;

  public function parse(HtmlParseRequest $request): ContentNode;

}
