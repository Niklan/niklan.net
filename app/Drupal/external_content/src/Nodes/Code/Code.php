<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Code;

use Drupal\external_content\Nodes\Content\Content;

final class Code extends Content {

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
