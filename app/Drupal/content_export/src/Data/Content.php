<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

use Drupal\content_export\Contract\MarkdownSourceInterface;

/**
 * Provides an object for contents storage.
 */
final class Content implements \IteratorAggregate {

  /**
   * The content items.
   *
   * @var \Drupal\content_export\Contract\MarkdownSourceInterface[]
   */
  protected array $items = [];

  /**
   * Add single content into collection.
   *
   * @param \Drupal\content_export\Contract\MarkdownSourceInterface $content
   *   The content instance.
   */
  public function addContent(MarkdownSourceInterface $content): self {
    $this->items[] = $content;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \Traversable {
    return new \ArrayIterator($this->items);
  }

}
