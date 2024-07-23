<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Environment;

use Drupal\external_content\Data\PrioritizedList;
use League\Config\ConfigurationProviderInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Represents an external content environment.
 */
interface EnvironmentInterface extends EventDispatcherInterface, ListenerProviderInterface, ConfigurationProviderInterface {

  public function id(): string;

  public function getHtmlParsers(): PrioritizedList;

  public function getIdentifiers(): PrioritizedList;

  public function getFinders(): PrioritizedList;

  public function getRenderArrayBuilders(): PrioritizedList;

  public function getSerializers(): PrioritizedList;

  public function getLoaders(): PrioritizedList;

  public function getConverters(): PrioritizedList;

  public function getBundlers(): PrioritizedList;

}
