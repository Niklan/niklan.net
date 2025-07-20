<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Extension\ContainerExtensionManager;
use Drupal\niklan\ExternalContent\Nodes\Callout\ArrayBuilder as CalloutBuilder;
use Drupal\niklan\ExternalContent\Nodes\CalloutBody\ArrayBuilder as CalloutBodyBuilder;
use Drupal\niklan\ExternalContent\Nodes\CalloutTitle\ArrayBuilder as CalloutTitleBuilder;
use Drupal\niklan\ExternalContent\Nodes\CodeBlock\ArrayBuilder as CodeBlockBuilder;
use Drupal\niklan\ExternalContent\Nodes\MediaReference\ArrayBuilder as MediaReferenceBuilder;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Builder\Array\Builder>>
 */
#[Autoconfigure(
  tags: [
    ['name' => ContainerExtensionManager::TAG_NAME, 'id' => self::ID],
  ],
)]
final readonly class ArrayBuilderExtension implements Extension {

  public const string ID = 'niklan.array_builder';

  public function register(object $target): void {
    $target->add(new CalloutBuilder());
    $target->add(new CalloutBodyBuilder());
    $target->add(new CalloutTitleBuilder());
    $target->add(new CodeBlockBuilder());
    $target->add(new MediaReferenceBuilder());
  }

}
