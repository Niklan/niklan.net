<?php

declare(strict_types=1);

namespace Drupal\external_content\Builder;

use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderManagerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;
use Drupal\external_content\Exception\MissingContainerDefinitionException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@selfdoc}
 */
final class RenderArrayBuilderManager implements RenderArrayBuilderManagerInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private ContainerInterface $container,
    private ChildRenderArrayBuilderInterface $childRenderArrayBuilder,
    private array $renderArrayBuilders = [],
  ) {}

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, EnvironmentInterface $environment): RenderArrayBuilderResult {
    $this->childRenderArrayBuilder->setEnvironment($environment);

    return $this->childRenderArrayBuilder->build($node);
  }

  /**
   * {@inheritdoc}
   */
  public function get(string $builder_id): RenderArrayBuilderInterface {
    if (!$this->has($builder_id)) {
      throw new MissingContainerDefinitionException(
        type: 'render_array_builder',
        id: $builder_id,
      );
    }

    $service = $this->renderArrayBuilders[$builder_id]['service'];

    return $this->container->get($service);
  }

  /**
   * {@inheritdoc}
   */
  public function has(string $builder_id): bool {
    return \array_key_exists($builder_id, $this->renderArrayBuilders);
  }

  /**
   * {@inheritdoc}
   */
  public function list(): array {
    return $this->renderArrayBuilders;
  }

}
