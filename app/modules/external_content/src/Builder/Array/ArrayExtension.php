<?php

declare(strict_types=1);

namespace Drupal\external_content\Builder\Array;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Extension\ContainerExtensionManager;
use Drupal\external_content\Nodes\Format\ArrayBuilder as FormatBuilder;
use Drupal\external_content\Nodes\Heading\ArrayBuilder as HeadingBuilder;
use Drupal\external_content\Nodes\HtmlElement\ArrayBuilder as HtmlElementBuilder;
use Drupal\external_content\Nodes\Text\ArrayBuilder as TextBuilder;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Builder\Array\Builder>>
 */
#[Autoconfigure(
  tags: [
    ['name' => ContainerExtensionManager::TAG_NAME, 'id' => self::ID],
  ],
)]
final readonly class ArrayExtension implements Extension {

  public const string ID = 'external_content.array_builder';

  public function register(object $target): void {
    // @phpstan-ignore-next-line new.deprecatedClass
    $target->add(new FormatBuilder());
    // @phpstan-ignore-next-line new.deprecatedClass
    $target->add(new HeadingBuilder());
    $target->add(new HtmlElementBuilder());
    $target->add(new TextBuilder());
  }

}
