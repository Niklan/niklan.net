<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure\Nodes;

final class ParagraphNode extends ElementNode {

  public function __construct() {
    parent::__construct('paragraph');
  }

}
