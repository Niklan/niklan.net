<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\DataStructure;

use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\ContentNode;

interface HtmlNodeParser {

  public function supports(HtmlParseRequest $request): bool;

  public function parse(HtmlParseRequest $request): ContentNode;

}
