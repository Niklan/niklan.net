<?php

declare(strict_types=1);

namespace Drupal\external_content\Environment;

use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Converter\ConverterInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Extension\ConfigurableExtensionInterface;
use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Identifier\IdentifierInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\EventListener;
use Drupal\external_content\Data\PrioritizedList;
use League\Config\Configuration;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Provides an environment for a specific external content processing.
 */
final class Environment implements EnvironmentInterface, EnvironmentBuilderInterface {

  /**
   * @var \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Parser\HtmlParserInterface>
   */
  protected PrioritizedList $htmlParsers;

  /**
   * @var \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Identifier\IdentifierInterface>
   */
  protected PrioritizedList $identifiers;

  /**
   * @var \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Finder\FinderInterface>
   */
  protected PrioritizedList $finders;

  /**
   * @var \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Data\EventListener> */
  protected PrioritizedList $eventListeners;

  /**
   * @var \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Serializer\SerializerInterface>
   */
  protected PrioritizedList $serializers;

  /**
   * @var \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface>
   */
  protected PrioritizedList $renderArrayBuilders;
  protected ?EventDispatcherInterface $eventDispatcher = NULL;

  /**
   * @var \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Loader\LoaderInterface>
   */
  protected PrioritizedList $loaders;

  /**
   * @var \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Converter\ConverterInterface>
   */
  protected PrioritizedList $converters;
  protected Configuration $configuration;

  /**
   * @var \Drupal\external_content\Data\PrioritizedList<\Drupal\external_content\Contract\Bundler\BundlerInterface>
   */
  protected PrioritizedList $bundlers;

  /**
   * Constructs a new Environment instance.
   */
  public function __construct(
    private readonly string $id,
    array $configuration = [],
  ) {
    $this->configuration = new Configuration();
    $this->configuration->merge($configuration);
    $this->finders = new PrioritizedList();
    $this->identifiers = new PrioritizedList();
    $this->bundlers = new PrioritizedList();
    $this->converters = new PrioritizedList();
    $this->htmlParsers = new PrioritizedList();
    $this->renderArrayBuilders = new PrioritizedList();
    $this->eventListeners = new PrioritizedList();
    $this->serializers = new PrioritizedList();
    $this->loaders = new PrioritizedList();
  }

  #[\Override]
  public function id(): string {
    return $this->id;
  }

  #[\Override]
  public function getHtmlParsers(): PrioritizedList {
    return $this->htmlParsers;
  }

  #[\Override]
  public function getIdentifiers(): PrioritizedList {
    return $this->identifiers;
  }

  #[\Override]
  public function getConfiguration(): ConfigurationInterface {
    return $this->configuration->reader();
  }

  #[\Override]
  public function getFinders(): PrioritizedList {
    return $this->finders;
  }

  #[\Override]
  public function getRenderArrayBuilders(): PrioritizedList {
    return $this->renderArrayBuilders;
  }

  #[\Override]
  public function dispatch(object $event): object {
    if ($this->eventDispatcher) {
      return $this->eventDispatcher->dispatch($event);
    }

    foreach ($this->getListenersForEvent($event) as $event_listener) {
      if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
        break;
      }

      $event_listener($event);
    }

    return $event;
  }

  /**
   * @return \Generator<callable(object $event): void>
   */
  #[\Override]
  public function getListenersForEvent(object $event): \Generator {
    foreach ($this->eventListeners as $event_listener) {
      \assert($event_listener instanceof EventListener);

      if (!\is_a($event, $event_listener->getEvent())) {
        continue;
      }

      yield static fn (object $event) => \call_user_func(
        $event_listener->getListener(),
        $event,
      );
    }
  }

  #[\Override]
  public function getSerializers(): PrioritizedList {
    return $this->serializers;
  }

  #[\Override]
  public function getLoaders(): PrioritizedList {
    return $this->loaders;
  }

  #[\Override]
  public function getConverters(): PrioritizedList {
    return $this->converters;
  }

  #[\Override]
  public function getBundlers(): PrioritizedList {
    return $this->bundlers;
  }

  #[\Override]
  public function addHtmlParser(HtmlParserInterface $parser, int $priority = 0): EnvironmentBuilderInterface {
    $this->htmlParsers->add($parser, $priority);
    $this->injectDependencies($parser);

    return $this;
  }

  #[\Override]
  public function addIdentifier(IdentifierInterface $identifier, int $priority = 0): EnvironmentBuilderInterface {
    $this->identifiers->add($identifier, $priority);
    $this->injectDependencies($identifier);

    return $this;
  }

  #[\Override]
  public function addFinder(FinderInterface $finder, int $priority = 0): EnvironmentBuilderInterface {
    $this->finders->add($finder, $priority);
    $this->injectDependencies($finder);

    return $this;
  }

  #[\Override]
  public function addEventListener(string $event_class, callable $listener, int $priority = 0): self {
    $this->eventListeners->add(
      item: new EventListener($event_class, \Closure::fromCallable($listener)),
      priority: $priority,
    );

    return $this;
  }

  #[\Override]
  public function setEventDispatcher(EventDispatcherInterface $event_dispatcher): self {
    $this->eventDispatcher = $event_dispatcher;

    return $this;
  }

  #[\Override]
  public function addRenderArrayBuilder(RenderArrayBuilderInterface $builder, int $priority = 0): self {
    $this->renderArrayBuilders->add($builder, $priority);
    $this->injectDependencies($builder);

    return $this;
  }

  #[\Override]
  public function addSerializer(SerializerInterface $serializer, int $priority = 0): self {
    $this->serializers->add($serializer, $priority);
    $this->injectDependencies($serializer);

    return $this;
  }

  #[\Override]
  public function addExtension(ExtensionInterface $extension): self {
    if ($extension instanceof ConfigurableExtensionInterface) {
      $extension->configureSchema($this->configuration);
    }

    $extension->register($this);

    return $this;
  }

  #[\Override]
  public function addLoader(LoaderInterface $loader, int $priority = 0): self {
    $this->loaders->add($loader, $priority);
    $this->injectDependencies($loader);

    return $this;
  }

  #[\Override]
  public function addConverter(ConverterInterface $converter, int $priority = 0): self {
    $this->converters->add($converter, $priority);
    $this->injectDependencies($converter);

    return $this;
  }

  #[\Override]
  public function addBundler(BundlerInterface $bundler, int $priority = 0): self {
    $this->bundlers->add($bundler, $priority);
    $this->injectDependencies($bundler);

    return $this;
  }

  private function injectDependencies(object $object): void {
    if ($object instanceof EnvironmentAwareInterface) {
      $object->setEnvironment($this);
    }

    if (!($object instanceof ConfigurationAwareInterface)) {
      return;
    }

    $object->setConfiguration($this->configuration);
  }

}
