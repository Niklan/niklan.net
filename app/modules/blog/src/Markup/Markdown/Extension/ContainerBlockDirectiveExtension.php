<?php

declare(strict_types=1);

namespace Drupal\app_blog\Markup\Markdown\Extension;

use Drupal\app_blog\Markup\Markdown\Node\ContainerBlockDirective;
use Drupal\app_blog\Markup\Markdown\Parser\ContainerBlockDirectiveStartParser;
use Drupal\app_blog\Markup\Markdown\Renderer\ContainerBlockDirectiveRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * Provides container directive for Markdown.
 *
 * Container Directive it is a simply Markdown Syntax that can be used to
 * create different types of containers using similar syntax.
 *
 * Example:
 * @code
 *  :::name[inline-content](argument){#id .class key=value}
 *    Contents of the directive.
 *  :::
 * @endcode
 *
 * Analogous to fenced code blocks, an arbitrary number of colons greater or
 * equal three could be used as long as the closing line is longer than the
 * opening line. That way, you can even nest blocks (think of div) by using
 * successively fewer colons for each containing block.
 *
 * @see https://talk.commonmark.org/t/generic-directives-plugins-syntax
 * @see https://github.com/commonmark/commonmark-spec/wiki/Generic-Directive-Extension-List
 *
 * @ingroup markdown
 */
final class ContainerBlockDirectiveExtension implements ExtensionInterface {

  #[\Override]
  public function register(EnvironmentBuilderInterface $environment): void {
    $environment->addBlockStartParser(new ContainerBlockDirectiveStartParser(), 70);
    $environment->addRenderer(
      nodeClass: ContainerBlockDirective::class,
      renderer: new ContainerBlockDirectiveRenderer(),
    );
  }

}
