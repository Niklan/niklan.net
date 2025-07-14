<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Utils\Registry;
use Drupal\niklan\ExternalContent\Nodes\ArticleLink\RenderArrayBuilder as ArticleLinkBuilder;
use Drupal\niklan\ExternalContent\Nodes\Callout\RenderArrayBuilder as CalloutBuilder;
use Drupal\niklan\ExternalContent\Nodes\CodeBlock\RenderArrayBuilder as CodeBlockBuilder;
use Drupal\niklan\ExternalContent\Nodes\MediaReference\RenderArrayBuilder as MediaReferenceBuilder;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Exporter\RenderArray\Builder>>
 */
final readonly class RenderArrayBuilderExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new CodeBlockBuilder());
    $target->add(new CalloutBuilder());
    $target->add(new MediaReferenceBuilder());
    $target->add(new ArticleLinkBuilder(), 10);
  }

}
