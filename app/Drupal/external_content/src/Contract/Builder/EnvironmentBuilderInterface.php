<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Builder;

use Drupal\external_content\Contract\Converter\ConverterInterface;
use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Identifier\IdentifierInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Parser\ParserInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
use League\Config\ConfigurationProviderInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Represents an interface for environment builder.
 */
interface EnvironmentBuilderInterface extends ConfigurationProviderInterface {

  /**
   * {@selfdoc}
   */
  public function addParser(ParserInterface $parser, int $priority = 0): self;

  /**
   * {@selfdoc}
   */
  public function addIdentifier(IdentifierInterface $identifier, int $priority = 0): self;

  /**
   * {@selfdoc}
   */
  public function addFinder(FinderInterface $finder, int $priority = 0): self;

  /**
   * {@selfdoc}
   */
  public function addBuilder(BuilderInterface $builder, int $priority = 0): self;

  /**
   * {@selfdoc}
   */
  public function addSerializer(NodeSerializerInterface $serializer, int $priority = 0): self;

  /**
   * {@selfdoc}
   */
  public function addEventListener(string $event_class, callable $listener, int $priority = 0): self;

  /**
   * {@selfdoc}
   */
  public function setEventDispatcher(EventDispatcherInterface $event_dispatcher): self;

  /**
   * {@selfdoc}
   */
  public function addExtension(ExtensionInterface $extension): self;

  /**
   * {@selfdoc}
   */
  public function addLoader(LoaderInterface $loader, int $priority): self;

  /**
   * {@selfdoc}
   */
  public function addConverter(ConverterInterface $converter, int $priority): self;

}
