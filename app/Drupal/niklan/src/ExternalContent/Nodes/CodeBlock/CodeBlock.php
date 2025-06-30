<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\CodeBlock;

use Drupal\external_content\Nodes\Content\Content;

final class CodeBlock extends Content {

  public function __construct(
    string $code,
  ) {
    parent::__construct();
    $this->setCode($code);
  }

  public function setCode(string $code): void {
    $this->properties->setProperty('code', $code);
  }

  public function getCode(): string {
    return $this->properties->getProperty('code');
  }

  public static function getType(): string {
    return 'code';
  }

}
