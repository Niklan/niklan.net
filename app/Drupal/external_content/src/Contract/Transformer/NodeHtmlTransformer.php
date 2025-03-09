<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Transformer;

use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Transformer\Html\HtmlTransformerContext;

interface NodeHtmlTransformer {

  public function supports(\DOMNode $node, HtmlTransformerContext $context): bool;

  public function transform(\DOMNode $node, HtmlTransformerContext $context): ContentNode;

}
