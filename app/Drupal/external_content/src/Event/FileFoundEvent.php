<?php declare(strict_types = 1);

namespace Drupal\external_content\Event;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Source\File;

/**
 * {@selfdoc}
 */
final class FileFoundEvent extends Event {

  /**
   * Constructs a new FileFoundEvent instance.
   */
  public function __construct(
    public readonly File $file,
    public readonly EnvironmentInterface $environment,
  ) {}

}
