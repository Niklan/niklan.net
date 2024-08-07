<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Builder;

use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Converter\ConverterInterface;
use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Identifier\IdentifierInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use League\Config\ConfigurationProviderInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Represents an interface for environment builder.
 */
interface EnvironmentBuilderInterface extends ConfigurationProviderInterface {

  public function addHtmlParser(HtmlParserInterface $parser, int $priority = 0): self;

  public function addIdentifier(IdentifierInterface $identifier, int $priority = 0): self;

  public function addFinder(FinderInterface $finder, int $priority = 0): self;

  public function addRenderArrayBuilder(RenderArrayBuilderInterface $builder, int $priority = 0): self;

  public function addSerializer(SerializerInterface $serializer, int $priority = 0): self;

  public function addEventListener(string $event_class, callable $listener, int $priority = 0): self;

  public function setEventDispatcher(EventDispatcherInterface $event_dispatcher): self;

  public function addExtension(ExtensionInterface $extension): self;

  public function addLoader(LoaderInterface $loader, int $priority = 0): self;

  public function addConverter(ConverterInterface $converter, int $priority = 0): self;

  public function addBundler(BundlerInterface $bundler, int $priority = 0): self;

}
