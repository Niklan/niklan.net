<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Nodes\ContentNode;

final class HtmlElementNode extends ContentNode {

  public function __construct(
    public readonly string $nodeName,
  ) {
    parent::__construct();
  }

  public static function getType(): string {
    return 'html_element';
  }

}
