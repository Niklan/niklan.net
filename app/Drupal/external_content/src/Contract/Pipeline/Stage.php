<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Pipeline;

interface Stage {

  public function process(Context $context, Config $config): Context;

}
