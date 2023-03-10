<?php declare(strict_types = 1);

namespace Drupal\content_export\Manager;

use Drupal\content_export\Contract\MarkdownBuilderInterface;
use Drupal\content_export\Contract\MarkdownSourceInterface;

/**
 * Provides a Markdown builder manager.
 */
final class MarkdownBuilderManager {

  /**
   * An array with Markdown builders.
   *
   * @var \Drupal\content_export\Contract\MarkdownBuilderInterface[]
   */
  protected array $builders = [];

  /**
   * Adds Markdown builder.
   *
   * @param \Drupal\content_export\Contract\MarkdownBuilderInterface $builder
   *   The builder instance.
   */
  public function addBuilder(MarkdownBuilderInterface $builder): void {
    $this->builders[] = $builder;
  }

  /**
   * Builds Markdown from source.
   *
   * @param \Drupal\content_export\Contract\MarkdownSourceInterface $source
   *   The source data.
   *
   * @return string
   *   The Markdown content.
   *
   * @throws \Exception
   */
  public function buildMarkdown(MarkdownSourceInterface $source): string {
    $builder = $this->getBuilderBySource($source);

    if (!$builder) {
      $message = \sprintf('No builder found for %s', $source::class);

      throw new \Exception($message);
    }

    return $builder->build($source);
  }

  /**
   * Gets a Markdown builder by source data.
   *
   * @param \Drupal\content_export\Contract\MarkdownSourceInterface $source
   *   The source data.
   *
   * @return \Drupal\content_export\Contract\MarkdownBuilderInterface|null
   *   The builder instance if found.
   */
  public function getBuilderBySource(MarkdownSourceInterface $source): ?MarkdownBuilderInterface {
    foreach ($this->builders as $builder) {
      if ($builder::isApplicable($source)) {
        return $builder;
      }
    }

    return NULL;
  }

}
