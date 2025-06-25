<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer\Html;

use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\Content\Content;

interface Parser {

  public function supports(HtmlParseRequest $request): bool;

  public function parse(HtmlParseRequest $request): Content;

}
