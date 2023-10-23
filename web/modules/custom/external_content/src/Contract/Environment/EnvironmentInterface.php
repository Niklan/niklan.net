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
   * {@selfdoc}
   */
  public function getParsers(): PrioritizedList;

  /**
   * {@selfdoc}
   */
  public function getBundlers(): PrioritizedList;

  /**
   * {@selfdoc}
   */
  public function getConfiguration(): Configuration;

  /**
   * {@selfdoc}
   */
  public function getFinders(): PrioritizedList;

  /**
   * {@selfdoc}
   */
  public function getBuilders(): PrioritizedList;

  /**
   * {@selfdoc}
   */
  public function getSerializers(): PrioritizedList;

  /**
   * {@selfdoc}
   */
  public function getLoaders(): PrioritizedList;

}
