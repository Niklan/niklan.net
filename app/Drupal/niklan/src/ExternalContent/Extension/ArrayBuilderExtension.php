<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Utils\Registry;
use Drupal\niklan\ExternalContent\Nodes\CodeBlock\ArrayBuilder as CodeBlockBuilder;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Exporter\Array\ArrayBuilder>>
 */
final readonly class ArrayBuilderExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new CodeBlockBuilder());
  }

}
