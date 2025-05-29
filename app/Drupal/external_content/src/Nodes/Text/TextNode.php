<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Text;

use Drupal\external_content\Nodes\ContentNode;

final class TextNode extends ContentNode {

  public function __construct(
    string $text,
  ) {
    $this->setText($text);
  }

  public function setText(string $text): void {
    $this->properties->setProperty('text', $text);
  }

  public function getText(): string {
    return $this->properties->getProperty('text');
  }

  public static function getType(): string {
    return 'text';
  }

}
