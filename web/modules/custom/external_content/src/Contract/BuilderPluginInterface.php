<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

/**
 * Provides a builder plugin interface.
 *
 * Builder plugins is responsible for builder Drupal render array that will be
 * used to display a parsed external content.
 */
interface BuilderPluginInterface {

  /**
   * Determines is plugin is able to build a provided element.
   *
   * @param \Drupal\external_content\Contract\ElementInterface $element
   *   The element to build.
   *
   * @return bool
   *   TRUE if plugin is able to build it, FALSE otherwise.
   */
  public static function isApplicable(ElementInterface $element): bool;

  /**
   * Builds a render array for a specific element.
   *
   * The element builder is also responsible for calling build processes on
   * children elements.
   *
   * @param \Drupal\external_content\Contract\ElementInterface $element
   *   The element to build.
   * @param array $children
   *   An array with children built elements.
   *
   * @return array
   *   A renderable array result for provided element.
   *
   * @todo Add BuilderState param and DTO with additional data like builder
   *   plugin, external content, etc.
   */
  public function build(ElementInterface $element, array $children): array;

}
