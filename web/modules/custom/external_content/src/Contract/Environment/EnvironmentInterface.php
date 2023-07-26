<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Environment;

use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Data\PrioritizedList;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Represents an external content environment.
 */
interface EnvironmentInterface extends EventDispatcherInterface, ListenerProviderInterface {

  /**
   * Gets the HTML parsers.
   */
  public function getHtmlParsers(): PrioritizedList;

  /**
   * Gets the content bundlers.
   */
  public function getBundlers(): PrioritizedList;

  /**
   * Gets the configuration.
   */
  public function getConfiguration(): Configuration;

  /**
   * Gets the finders.
   */
  public function getFinders(): PrioritizedList;

  /**
   * Gets the builders.
   */
  public function getBuilders(): PrioritizedList;

}
