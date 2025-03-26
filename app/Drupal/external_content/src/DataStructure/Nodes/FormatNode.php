<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure\Nodes;

use Drupal\external_content\Domain\TextFormatType;

final class FormatNode extends ElementNode {

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
