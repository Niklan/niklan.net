<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Pipeline;

use Drupal\external_content\Pipeline\NullConfig;

interface Pipeline {

  public function addStage(Stage $stage, Config $config = new NullConfig()): void;

  public function run(Context $context): Context;

}
