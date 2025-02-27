<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Pipeline;

interface Pipeline {

  public function addStage(Stage $stage, ?Config $config = NULL): void;

  public function run(Context $context): Context;

}
