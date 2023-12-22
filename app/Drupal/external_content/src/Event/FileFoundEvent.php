<?php declare(strict_types = 1);

namespace Drupal\external_content\Event;

use Drupal\external_content\Source\File;

/**
 * {@selfdoc}
 */
final class FileFoundEvent extends Event {

  /**
   * Constructs a new FileFoundEvent instance.
   */
  public function __construct(
    public File $file,
  ) {}

}
