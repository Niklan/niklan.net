<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Parser\Html;

use Drupal\external_content\Nodes\Node;

interface Parser {

  public function supports(\DOMNode $dom_node): bool;

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node;

}
