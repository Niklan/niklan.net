<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer;

use Drupal\external_content\Importer\Html\HtmlParserRequest;
use Drupal\external_content\Node\ContentNode;

interface HtmlNodeParser {

  public function supports(HtmlParserRequest $request): bool;

  public function parse(HtmlParserRequest $request): ContentNode;

}
