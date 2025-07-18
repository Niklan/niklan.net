<?php

declare(strict_types=1);

namespace Drupal\niklan\Markup\Markdown\Extension;

use Drupal\niklan\Markup\Markdown\Node\LeafBlockDirective;
use Drupal\niklan\Markup\Markdown\Parser\LeafBlockDirectiveStartParser;
use Drupal\niklan\Markup\Markdown\Renderer\LeafBlockDirectiveRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * Provides leaf directive for Markdown.
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
 * @see \Drupal\niklan\Markup\Markdown\Node\BlockDirective
 *
 * @ingroup markdown
 */
final class LeafBlockDirectiveExtension implements ExtensionInterface {

  #[\Override]
  public function register(EnvironmentBuilderInterface $environment): void {
    $environment->addBlockStartParser(new LeafBlockDirectiveStartParser(), 70);
    $environment->addRenderer(
      nodeClass: LeafBlockDirective::class,
      renderer: new LeafBlockDirectiveRenderer(),
    );
  }

}
