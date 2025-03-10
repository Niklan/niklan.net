<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer;

use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Transformer\Html\HtmlImporterContext;

interface HtmlNodeParser {

  public function supports(\DOMNode $node, HtmlImporterContext $context): bool;

  public function parse(\DOMNode $node, HtmlImporterContext $context): ContentNode;

}
