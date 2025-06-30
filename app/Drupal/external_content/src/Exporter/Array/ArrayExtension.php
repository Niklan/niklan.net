<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Nodes\Content\ArrayBuilder as ContentBuilder;
use Drupal\external_content\Nodes\HtmlElement\ArrayBuilder as HtmlElementBuilder;
use Drupal\external_content\Utils\Registry;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Exporter\Array\ArrayElementBuilder>>
 */
final readonly class ArrayExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new HtmlElementBuilder());
    $target->add(new ContentBuilder(), -100);
  }

}
