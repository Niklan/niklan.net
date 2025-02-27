<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Pipeline;

interface Stage {

  /**
   * @template TInput of \Drupal\external_content\Contract\Pipeline\Context
   * @template TOutput of \Drupal\external_content\Contract\Pipeline\Context
   * @param TInput $context
   * @param \Drupal\external_content\Contract\Pipeline\Drupal\external_content\Contract\Pipeline\Config $config
   * @return TOutput
   */
  public function process(Context $context, Config $config): Context;

}
