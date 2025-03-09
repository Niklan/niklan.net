<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Pipeline;

use Psr\Log\LoggerInterface;

interface PipelineContext {

  public function getLogger(): LoggerInterface;

}
