<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Extension;

use Drupal\niklan\CommonMark\Block\ContainerBlockDirective;
use Drupal\niklan\CommonMark\Parser\ContainerBlockDirectiveStartParser;
use Drupal\niklan\CommonMark\Renderer\ContainerBlockDirectiveRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * {@selfdoc}
 *
 * Container Directive it is a simply Markdown Syntax that can be used to create
 * different types of containers using similar syntax.
 *
 * Example:
 * @code
 *  :::name[inline-content](argument){#id .class key=value}
 *  Note contents.
 *  :::
 * @endcode
 *
 * Analogous to fenced code blocks, an arbitrary number of colons greater or
 * equal three could be used as long as the closing line is longer than the
 * opening line. That way, you can even nest blocks (think divs) by using
 * successively fewer colons for each containing block.
 *
 * @see https://talk.commonmark.org/t/generic-directives-plugins-syntax
 * @see https://github.com/commonmark/commonmark-spec/wiki/Generic-Directive-Extension-List
 *
 * @ingroup markdown
 */
final class ContainerBlockDirectiveExtension implements ExtensionInterface {

  /**
   * {@inheritdoc}
   */
  public function register(EnvironmentBuilderInterface $environment): void {
    $environment->addBlockStartParser(new ContainerBlockDirectiveStartParser(), 70);
    $environment->addRenderer(
      nodeClass: ContainerBlockDirective::class,
      renderer: new ContainerBlockDirectiveRenderer(),
    );
  }

}
