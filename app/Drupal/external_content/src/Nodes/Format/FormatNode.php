<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Format;

use Drupal\external_content\Domain\TextFormatType;
use Drupal\external_content\Nodes\ContentNode;

final class FormatNode extends ContentNode {

  public function __construct(
    TextFormatType $format,
  ) {
    parent::__construct();
    $this->setFormat($format);
  }

  public function getFormat(): TextFormatType {
    return TextFormatType::from($this->getProperties()->getProperty('format'));
  }

  public function setFormat(TextFormatType $format): void {
    $this->getProperties()->setProperty('format', $format->value);
  }

  public static function getType(): string {
    return 'format';
  }

}
