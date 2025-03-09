<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Transformer;

use Psr\Log\LoggerInterface;

/**
 * Provides an interface for transformer related contexts.
 */
interface TransformerContext {

  public function getLogger(): LoggerInterface;

}
