<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Environment;

use Drupal\Core\Entity\FieldableEntityInterface;

final readonly class ViewRequest {

  public function __construct(
    public FieldableEntityInterface $entity,
    public string $viewMode,
  ) {}

}