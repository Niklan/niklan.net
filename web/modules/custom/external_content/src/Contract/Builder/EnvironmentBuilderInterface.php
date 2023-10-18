<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Builder;

use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Represents an interface for environment builder.
 */
interface EnvironmentBuilderInterface {

  /**
   * {@selfdoc}
   */
  public function addHtmlParser(HtmlParserInterface $parser, int $priority = 0): self;

  /**
   * {@selfdoc}
   */
  public function addBundler(BundlerInterface $bundler, int $priority = 0): self;

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
  public function addSerializer(NodeSerializerInterface $serializer, int $priority): self;

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

}