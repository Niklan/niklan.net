<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Environment;

use Drupal\external_content\Data\PrioritizedList;
use League\Config\ConfigurationProviderInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Represents an external content environment.
 */
interface EnvironmentInterface extends EventDispatcherInterface, ListenerProviderInterface, ConfigurationProviderInterface {

  /**
   * {@selfdoc}
   */
  public function getParsers(): PrioritizedList;

  /**
   * {@selfdoc}
   */
  public function getIdentifiers(): PrioritizedList;

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