<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Environment;

use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Data\PrioritizedList;

/**
 * Represents an external content environment.
 */
interface EnvironmentInterface {

  /**
   * Gets the HTML parsers.
   *
   * @return \Drupal\external_content\Data\PrioritizedList
   *   The list of HTML parsers.
   */
  public function getHtmlParsers(): PrioritizedList;

  /**
   * Gets the content groupers.
   *
   * @return \Drupal\external_content\Data\PrioritizedList
   *   The list of content groupers.
   */
  public function getGroupers(): PrioritizedList;

  /**
   * Gets the markup converters.
   *
   * @return \Drupal\external_content\Data\PrioritizedList
   *   The list of markup converters.
   */
  public function getMarkupConverters(): PrioritizedList;

  /**
   * Gets the markup pre-converters.
   *
   * @return \Drupal\external_content\Data\PrioritizedList
   *   The list of markup pre-converters.
   */
  public function getMarkupPreConverters(): PrioritizedList;

  /**
   * Gets the markup post-converters.
   *
   * @return \Drupal\external_content\Data\PrioritizedList
   *   The list of markup post-converters.
   */
  public function getMarkupPostConverters(): PrioritizedList;

  /**
   * Gets the configuration.
   *
   * @return \Drupal\external_content\Data\Configuration
   *   The environment configuration.
   */
  public function getConfiguration(): Configuration;

  /**
   * Gets the finders.
   *
   * @return \Drupal\external_content\Data\PrioritizedList
   *   The list of finders.
   */
  public function getFinders(): PrioritizedList;

}
