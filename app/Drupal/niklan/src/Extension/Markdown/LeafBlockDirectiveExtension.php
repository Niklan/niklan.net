<?php declare(strict_types = 1);

namespace Drupal\niklan\Extension\Markdown;

use Drupal\niklan\Node\Markdown\LeafBlockDirective;
use Drupal\niklan\Parser\Markdown\LeafBlockDirectiveStartParser;
use Drupal\niklan\Renderer\Markdown\LeafBlockDirectiveRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * {@selfdoc}
 *
 * Leaf Directive is very similar to Container Directive, the only difference is
 * that leaf block has no closing part and can only contain content using
 * inline value.
 *
 * Example:
 *
 * @code
 *  ::name[inline-content](argument){#id .class key=value}
 * @endcode
 *
 * @see \Drupal\niklan\Node\Markdown\BlockDirective
 *
 * @ingroup markdown
 */
final class LeafBlockDirectiveExtension implements ExtensionInterface {

  /**
   * {@inheritdoc}
   */
  public function register(EnvironmentBuilderInterface $environment): void {
    $environment->addBlockStartParser(new LeafBlockDirectiveStartParser(), 70);
    $environment->addRenderer(
      nodeClass: LeafBlockDirective::class,
      renderer: new LeafBlockDirectiveRenderer(),
    );
  }

}
