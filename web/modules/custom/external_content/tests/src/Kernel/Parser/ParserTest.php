<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Finder;

use Drupal\external_content\Contract\Parser\ParserInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Exception\MissingSourceParserException;
use Drupal\external_content\Node\Content;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use Prophecy\Argument;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Parser\Parser
 * @group external_content
 */
final class ParserTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  private function getParser(): ParserInterface {
    return $this->container->get(ParserInterface::class);
  }

  /**
   * {@selfdoc}
   */
  public function testEmptyFinder(): void {
    $source = $this->prophesize(SourceInterface::class);
    $source = $source->reveal();

    $environment = new Environment();
    $this->getParser()->setEnvironment($environment);
    $exception = new MissingSourceParserException($source, $environment);
    self::expectExceptionObject($exception);
    $this->getParser()->parse($source);
  }

  /**
   * {@selfdoc}
   */
  public function testFooFinder(): void {
    $source = $this->prophesize(SourceInterface::class);
    $source = $source->reveal();
    // The parser facade should always support parse of any source.
    self::assertTrue($this->getParser()->supportsParse($source));

    $parser = $this->prophesize(ParserInterface::class);
    $parser
      ->supportsParse(Argument::cetera())
      ->willReturn(TRUE)
      ->shouldBeCalled();
    $parser
      ->parse(Argument::cetera())
      ->willReturn(new Content($source))
      ->shouldBeCalled();
    $parser = $parser->reveal();

    $environment = new Environment();
    $environment->addParser($parser);

    $this->getParser()->setEnvironment($environment);
    $this->getParser()->parse($source);
  }

}
