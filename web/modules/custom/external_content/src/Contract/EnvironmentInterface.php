<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\Configuration;

/**
 * Provides an interface for External Content environment.
 */
interface EnvironmentInterface {

  /**
   * Gets the HTML parsers.
   *
   * @return iterable
   */
  public function getHtmlParsers(): iterable;

  /**
   * Gets the content groupers.
   *
   * @return iterable
   */
  public function getGroupers(): iterable;

  /**
   * Gets the markup converters.
   *
   * @return iterable
   */
  public function getMarkupConverters(): iterable;

  public function getMarkupConverterPreprocessors(): iterable;

  public function getMarkupConverterPostprocessors(): iterable;

  /**
   *
   */
  public function getConfiguration(): Configuration;

  /**
   * @return iterable<\Drupal\external_content\Contract\FinderInterface>
   */
  public function getFinders(): iterable;

}
