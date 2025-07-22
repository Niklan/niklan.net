<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser\Array;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Extension\ContainerExtensionManager;
use Drupal\external_content\Nodes\Format\ArrayParser as FormatParser;
use Drupal\external_content\Nodes\Heading\ArrayParser as HeadingParser;
use Drupal\external_content\Nodes\HtmlElement\ArrayParser as HtmlElementParser;
use Drupal\external_content\Nodes\Text\ArrayParser as TextParser;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Parser\Array\Parser>>
 */
#[Autoconfigure(
  tags: [
    ['name' => ContainerExtensionManager::TAG_NAME, 'id' => self::ID],
  ],
)]
final readonly class ArrayExtension implements Extension {

  public const string ID = 'external_content.array_parser';

  public function register(object $target): void {
    // @phpstan-ignore-next-line new.deprecatedClass
    $target->add(new HeadingParser());
    $target->add(new TextParser());
    // @phpstan-ignore-next-line new.deprecatedClass
    $target->add(new FormatParser());
    $target->add(new HtmlElementParser());
  }

}
