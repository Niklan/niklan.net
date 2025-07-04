<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Parser\Html;

use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Parser\Html\HtmlParseRequest;

interface Parser {

  public function supports(HtmlParseRequest $request): bool;

  public function parse(HtmlParseRequest $request): Node;

}
