<?php

declare(strict_types=1);

namespace Drupal\external_content\Builder\RenderArray;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Extension\ContainerExtensionManager;
use Drupal\external_content\Nodes\Format\RenderArrayBuilder as FormatBuilder;
use Drupal\external_content\Nodes\Heading\RenderArrayBuilder as HeadingBuilder;
use Drupal\external_content\Nodes\HtmlElement\RenderArrayBuilder as HtmlElementBuilder;
use Drupal\external_content\Nodes\Text\RenderArrayBuilder as TextBuilder;
use Drupal\external_content\Utils\Registry;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Builder\RenderArray\Builder>>>
 */
#[Autoconfigure(
  tags: [
    ['name' => ContainerExtensionManager::TAG_NAME, 'id' => self::ID],
  ],
)]
final class RenderArrayExtension implements Extension {

  public const string ID = 'external_content.render_array_builder';

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new TextBuilder());
    $target->add(new FormatBuilder());
    $target->add(new HeadingBuilder());
    $target->add(new HtmlElementBuilder(), -100);
  }

}
