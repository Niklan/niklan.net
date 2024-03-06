<?php declare(strict_types = 1);

namespace Drupal\external_content\Environment;

use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Converter\ConverterInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Extension\ConfigurableExtensionInterface;
use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Identifier\IdentifierInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Parser\ParserInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
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
   * {@selfdoc}
   */
  protected PrioritizedList $parsers;

  /**
   * {@selfdoc}
   */
  protected PrioritizedList $identifiers;

  /**
   * {@selfdoc}
   */
  protected PrioritizedList $finders;

  /**
   * {@selfdoc}
   */
  protected PrioritizedList $eventListeners;

  /**
   * {@selfdoc}
   */
  protected PrioritizedList $serializers;

  /**
   * {@selfdoc}
   */
  protected PrioritizedList $builders;

  /**
   * {@selfdoc}
   */
  protected ?EventDispatcherInterface $eventDispatcher = NULL;

  /**
   * {@selfdoc}
   */
  protected PrioritizedList $loaders;

  /**
   * {@selfdoc}
   */
  protected PrioritizedList $converters;

  /**
   * {@selfdoc}
   */
  protected Configuration $configuration;

  /**
   * {@selfdoc}
   */
  protected PrioritizedList $bundlers;

  /**
   * Constructs a new Environment instance.
   */
  public function __construct(
    array $configuration = [],
    private readonly ?string $id = NULL,
  ) {
    $this->configuration = new Configuration();
    $this->configuration->merge($configuration);
    $this->finders = new PrioritizedList();
    $this->identifiers = new PrioritizedList();
    $this->bundlers = new PrioritizedList();
    $this->converters = new PrioritizedList();
    $this->parsers = new PrioritizedList();
    $this->builders = new PrioritizedList();
    $this->eventListeners = new PrioritizedList();
    $this->serializers = new PrioritizedList();
    $this->loaders = new PrioritizedList();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function id(): ?string {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getParsers(): PrioritizedList {
    return $this->parsers;
  }

  /**
   * {@inheritdoc}
   */
  public function getIdentifiers(): PrioritizedList {
    return $this->identifiers;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration(): ConfigurationInterface {
    return $this->configuration->reader();
  }

  /**
   * {@inheritdoc}
   */
  public function getFinders(): PrioritizedList {
    return $this->finders;
  }

  /**
   * {@inheritdoc}
   */
  public function getBuilders(): PrioritizedList {
    return $this->builders;
  }

  /**
   * {@inheritdoc}
   */
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
   * {@inheritdoc}
   */
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

  /**
   * {@inheritdoc}
   */
  public function getSerializers(): PrioritizedList {
    return $this->serializers;
  }

  /**
   * {@inheritdoc}
   */
  public function getLoaders(): PrioritizedList {
    return $this->loaders;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getConverters(): PrioritizedList {
    return $this->converters;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getBundlers(): PrioritizedList {
    return $this->bundlers;
  }

  /**
   * {@inheritdoc}
   */
  public function addParser(ParserInterface $parser, int $priority = 0): EnvironmentBuilderInterface {
    $this->parsers->add($parser, $priority);
    $this->injectDependencies($parser);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addIdentifier(IdentifierInterface $identifier, int $priority = 0): EnvironmentBuilderInterface {
    $this->identifiers->add($identifier, $priority);
    $this->injectDependencies($identifier);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addFinder(FinderInterface $finder, int $priority = 0): EnvironmentBuilderInterface {
    $this->finders->add($finder, $priority);
    $this->injectDependencies($finder);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addEventListener(string $event_class, callable $listener, int $priority = 0): self {
    $this->eventListeners->add(
      item: new EventListener($event_class, $listener),
      priority: $priority,
    );

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setEventDispatcher(EventDispatcherInterface $event_dispatcher): self {
    $this->eventDispatcher = $event_dispatcher;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addBuilder(BuilderInterface $builder, int $priority = 0): self {
    $this->builders->add($builder, $priority);
    $this->injectDependencies($builder);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addSerializer(NodeSerializerInterface $serializer, int $priority = 0): self {
    $this->serializers->add($serializer, $priority);
    $this->injectDependencies($serializer);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addExtension(ExtensionInterface $extension): self {
    if ($extension instanceof ConfigurableExtensionInterface) {
      $extension->configureSchema($this->configuration);
    }

    $extension->register($this);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addLoader(LoaderInterface $loader, int $priority = 0): self {
    $this->loaders->add($loader, $priority);
    $this->injectDependencies($loader);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function addConverter(ConverterInterface $converter, int $priority = 0): self {
    $this->converters->add($converter, $priority);
    $this->injectDependencies($converter);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function addBundler(BundlerInterface $bundler, int $priority = 0): self {
    $this->bundlers->add($bundler, $priority);
    $this->injectDependencies($bundler);

    return $this;
  }

  /**
   * {@selfdoc}
   */
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
