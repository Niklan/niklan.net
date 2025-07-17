<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Environment;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Environment extends Plugin {

  public function __construct(
    public readonly string $id,
    public readonly ?TranslatableMarkup $label,
    public readonly ?string $deriver = NULL,
  ) {}

}
