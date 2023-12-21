<?php declare(strict_types = 1);

namespace Drupal\external_content\Builder;

use Drupal\external_content\Dto\ElementInterface;

/**
 * Provides an interface for building renderable array.
 *
 * Builder is responsible to build a renderable array from element(s) of
 * parsed content.
 */
interface ChainRenderArrayBuilderInterface {

  /**
   * Builds a provided element and all it's children.
   *
   * @param \Drupal\external_content\Dto\ElementInterface $element
   *   The element to build.
   *
   * @return array
   *   The result render array.
   */
  public function build(ElementInterface $element): array;

}
