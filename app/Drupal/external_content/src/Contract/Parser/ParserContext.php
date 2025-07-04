<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Parser;

use Psr\Log\LoggerInterface;

interface ParserContext {

  public function getLogger(): LoggerInterface;

}
