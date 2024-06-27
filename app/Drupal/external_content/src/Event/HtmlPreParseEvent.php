<?php

declare(strict_types=1);

namespace Drupal\external_content\Event;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\Data;

/**
 * {@selfdoc}
 */
final class HtmlPreParseEvent extends Event {

  /**
   * {@selfdoc}
   */
  public function __construct(
    public string $content,
    public Data $data,
    public EnvironmentInterface $environment,
  ) {}

}
