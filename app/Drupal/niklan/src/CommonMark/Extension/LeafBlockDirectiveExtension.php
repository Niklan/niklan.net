<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Extension;

use Drupal\niklan\CommonMark\Block\LeafBlockDirective;
use Drupal\niklan\CommonMark\Parser\LeafBlockDirectiveStartParser;
use Drupal\niklan\CommonMark\Renderer\LeafBlockDirectiveRenderer;
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
 * @code
 *  :::name[inline-content](argument){#id .class key=value}
 * @endcode
 *
 * @see \Drupal\niklan\CommonMark\Block\BlockDirective
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
