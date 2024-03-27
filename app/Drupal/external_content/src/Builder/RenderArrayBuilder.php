<?php declare(strict_types = 1);

namespace Drupal\external_content\Builder;

use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;

/**
 * Provides an external content render array builder.
 */
final class RenderArrayBuilder implements BuilderInterface, EnvironmentAwareInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, string $type = self::class, array $context = []): BuilderResultInterface {
    $initial_children = [];
    $children = $this->buildRecursive($node, $initial_children, $context);
    $build = $this->buildNode($node, $children, $context);

    return BuilderResult::renderArray($build);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsBuild(NodeInterface $node, string $type, array $context = []): bool {
    return $type === self::class;
  }

  /**
   * {@selfdoc}
   */
  protected function buildRecursive(NodeInterface $node, array &$children, array $context): mixed {
    // @todo Pass $this builder as an argument and let builder decide how to
    //   handle children.
    // @see \Drupal\niklan\CommonMark\Renderer\NoteRenderer::render
    $children_build = [];

    foreach ($node->getChildren() as $child) {
      $children_build[] = $this->buildRecursive($child, $children, $context);
    }

    return $this->buildNode($node, $children_build, $context);
  }

  /**
   * {@selfdoc}
   */
  protected function buildNode(NodeInterface $node, array $children, array $context): mixed {
    $context += ['children' => $children];

    foreach ($this->environment->getBuilders() as $builder) {
      \assert($builder instanceof BuilderInterface);

      if (!$builder->supportsBuild($node, self::class, $context)) {
        continue;
      }

      $result = $builder->build($node, self::class, $context);

      if ($result->isNotBuild()) {
        continue;
      }

      return $result->result();
    }

    // If build didn't happen, just return children. Most likely it's a root
    // element.
    return $children;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
