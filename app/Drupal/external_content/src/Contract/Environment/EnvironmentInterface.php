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

  /**
   * @return \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Parser\HtmlParserInterface>
   */
  public function getHtmlParsers(): PrioritizedList;

  /**
   * @return \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Identifier\IdentifierInterface>
   */
  public function getIdentifiers(): PrioritizedList;

  /**
   * @return \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Finder\FinderInterface>
   */
  public function getFinders(): PrioritizedList;

  /**
   * @return \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface>
   */
  public function getRenderArrayBuilders(): PrioritizedList;

  /**
   * @return \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Serializer\SerializerInterface>
   */
  public function getSerializers(): PrioritizedList;

  /**
   * @return \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Loader\LoaderInterface>
   */
  public function getLoaders(): PrioritizedList;

  /**
   * @return \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Converter\ConverterInterface>
   */
  public function getConverters(): PrioritizedList;

  /**
   * @return \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Bundler\BundlerInterface>
   */
  public function getBundlers(): PrioritizedList;

}
