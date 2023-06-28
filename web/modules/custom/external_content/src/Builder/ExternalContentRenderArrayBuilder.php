<?php declare(strict_types = 1);

namespace Drupal\external_content\Builder;

use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultRenderArrayInterface;
use Drupal\external_content\Contract\Builder\ExternalContentRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;
use Drupal\external_content\DependencyInjection\EnvironmentAwareClassResolverInterface;
use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Provides an external content render array builder.
 */
final class ExternalContentRenderArrayBuilder implements ExternalContentRenderArrayBuilderInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * Constructs a new ExternalContentRenderArrayBuilder instance.
   *
   * @param \Drupal\external_content\DependencyInjection\EnvironmentAwareClassResolverInterface $classResolver
   *   The class resolver.
   */
  public function __construct(
    protected EnvironmentAwareClassResolverInterface $classResolver,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function build(ExternalContentDocument $document): BuilderResultRenderArrayInterface {
    $children = $this->buildRecursive($document, []);

    return BuilderResult::renderArray($this->buildNode($document, $children));
  }

  /**
   * Builds an element with its children.
   */
  protected function buildRecursive(NodeInterface $node, array $children): array {
    $children_build = [];

    foreach ($node->getChildren() as $child) {
      $children_build = $this->buildRecursive($child, $children);
    }

    return $this->buildNode($node, $children_build);
  }

  /**
   * Builds a single node.
   */
  public function buildNode(NodeInterface $node, array $children): array {
    foreach ($this->environment->getBuilders() as $builder) {
      $instance = $this->classResolver->getInstance(
        $builder,
        BuilderInterface::class,
        $this->getEnvironment(),
      );
      \assert($instance instanceof BuilderInterface);
      $result = $instance->build($node, $children);

      if ($result->isNotBuild()) {
        continue;
      }

      \assert($result instanceof BuilderResultRenderArrayInterface);

      return $result->getRenderArray();
    }

    // If build didn't happen, just return children. Most likely it's a root
    // collection like SourceFileContent.
    return $children;
  }

  /**
   * {@inheritdoc}
   */
  public function getEnvironment(): EnvironmentInterface {
    return $this->environment;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
