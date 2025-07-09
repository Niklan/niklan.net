<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Parser\Html;

use Drupal\external_content\Nodes\Node;

interface ChildParser {

  public function parseChildren(\DOMNode $html_node, Node $content_node): void;

}
