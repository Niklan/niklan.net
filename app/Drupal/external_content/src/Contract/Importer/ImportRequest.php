<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer;

interface ImportRequest {

  public function getSource(): ImporterSource;

  public function getContext(): ImporterContext;

}
