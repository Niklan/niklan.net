<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Exporter\Array\Builder\ArrayBuilder;
use Drupal\external_content\Exporter\Array\Builder\ContentNodeBuilder;
use Drupal\external_content\Exporter\Array\Builder\ElementNodeBuilder;
use Drupal\external_content\Exporter\Array\Builder\LiteralNodeBuilder;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Exporter\Array\Builder\ArrayBuilder>
 */
final readonly class DefaultArrayBuilderExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof ArrayBuilder);
    $target->addBuilder(new ContentNodeBuilder(), -100);
    $target->addBuilder(new ElementNodeBuilder(), -90);
    $target->addBuilder(new LiteralNodeBuilder());
  }

}
