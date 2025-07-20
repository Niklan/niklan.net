<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Extension\ContainerExtensionManager;
use Drupal\niklan\ExternalContent\Nodes\ArticleLink\RenderArrayBuilder as ArticleLinkBuilder;
use Drupal\niklan\ExternalContent\Nodes\Callout\RenderArrayBuilder as CalloutBuilder;
use Drupal\niklan\ExternalContent\Nodes\CodeBlock\RenderArrayBuilder as CodeBlockBuilder;
use Drupal\niklan\ExternalContent\Nodes\MediaReference\RenderArrayBuilder as MediaReferenceBuilder;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Builder\RenderArray\Builder>>
 */
#[Autoconfigure(
  tags: [
    ['name' => ContainerExtensionManager::TAG_NAME, 'id' => self::ID],
  ],
)]
final readonly class RenderArrayBuilderExtension implements Extension {

  public const string ID = 'niklan.render_array_builder';

  public function __construct(
    private CodeBlockBuilder $codeBlockBuilder,
    private CalloutBuilder $calloutBuilder,
    private MediaReferenceBuilder $mediaReferenceBuilder,
    private ArticleLinkBuilder $articleLinkBuilder,
  ) {}

  public function register(object $target): void {
    $target->add($this->codeBlockBuilder);
    $target->add($this->calloutBuilder);
    $target->add($this->mediaReferenceBuilder);
    $target->add($this->articleLinkBuilder, 10);
  }

}
