<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Domain;

final readonly class SoftwareCompatibility {

  public function __construct(
    public string $name,
    public ?string $constraint = NULL,
  ) {}

}
