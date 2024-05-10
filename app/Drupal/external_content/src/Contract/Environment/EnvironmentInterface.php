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
  public function id(): string;

  /**
   * {@selfdoc}
   */
  public function getHtmlParsers(): PrioritizedList;

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
  public function getRenderArrayBuilders(): PrioritizedList;

  /**
   * {@selfdoc}
   */
  public function getSerializers(): PrioritizedList;

  /**
   * {@selfdoc}
   */
  public function getLoaders(): PrioritizedList;

  /**
   * {@selfdoc}
   */
  public function getConverters(): PrioritizedList;

  /**
   * {@selfdoc}
   */
  public function getBundlers(): PrioritizedList;

}
