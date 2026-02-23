<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Extension\ContainerExtensionManager;
use Drupal\app_blog\ExternalContent\Nodes\ArticleLink\RenderArrayBuilder as ArticleLinkBuilder;
use Drupal\app_blog\ExternalContent\Nodes\Callout\RenderArrayBuilder as CalloutBuilder;
use Drupal\app_blog\ExternalContent\Nodes\CodeBlock\RenderArrayBuilder as CodeBlockBuilder;
use Drupal\app_blog\ExternalContent\Nodes\Figcaption\RenderArrayBuilder as FigcaptionBuilder;
use Drupal\app_blog\ExternalContent\Nodes\Figure\RenderArrayBuilder as FigureBuilder;
use Drupal\app_blog\ExternalContent\Nodes\Footnote\RenderArrayBuilder as FootnoteBuilder;
use Drupal\app_blog\ExternalContent\Nodes\MediaReference\RenderArrayBuilder as MediaReferenceBuilder;
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

  public const string ID = 'app_blog.render_array_builder';

  public function __construct(
    private CodeBlockBuilder $codeBlockBuilder,
    private CalloutBuilder $calloutBuilder,
    private MediaReferenceBuilder $mediaReferenceBuilder,
    private ArticleLinkBuilder $articleLinkBuilder,
    private FootnoteBuilder $footnoteBuilder,
  ) {}

  public function register(object $target): void {
    $target->add($this->codeBlockBuilder);
    $target->add($this->calloutBuilder);
    $target->add($this->mediaReferenceBuilder);
    $target->add($this->articleLinkBuilder, 10);
    $target->add(new FigureBuilder());
    $target->add(new FigcaptionBuilder());
    $target->add($this->footnoteBuilder);
  }

}
