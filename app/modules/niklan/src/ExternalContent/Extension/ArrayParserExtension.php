<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Extension\ContainerExtensionManager;
use Drupal\niklan\ExternalContent\Nodes\Callout\ArrayParser as CalloutParser;
use Drupal\niklan\ExternalContent\Nodes\CalloutBody\ArrayParser as CalloutBodyParser;
use Drupal\niklan\ExternalContent\Nodes\CalloutTitle\ArrayParser as CalloutTitleParser;
use Drupal\niklan\ExternalContent\Nodes\CodeBlock\ArrayParser as CodeBlockParser;
use Drupal\niklan\ExternalContent\Nodes\MediaReference\ArrayParser as MediaReferenceParser;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Parser\Array\Parser>>
 */
#[Autoconfigure(
  tags: [
    ['name' => ContainerExtensionManager::TAG_NAME, 'id' => self::ID],
  ],
)]
final readonly class ArrayParserExtension implements Extension {

  public const string ID = 'niklan.array_parser';

  public function register(object $target): void {
    $target->add(new CodeBlockParser());
    $target->add(new CalloutParser());
    $target->add(new CalloutTitleParser());
    $target->add(new CalloutBodyParser());
    $target->add(new MediaReferenceParser());
  }

}
