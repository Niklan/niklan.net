<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Exception;

use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Exception\MissingHtmlParserException;
use Drupal\Tests\UnitTestCase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Exception\MissingHtmlParserException
 * @ingroup external_content
 */
final class MissingHtmlParserExceptionTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testException(): void {
    $available = $this->prophesize(HtmlParserInterface::class);
    $available = $available->reveal();

    $source = $this->prophesize(SourceInterface::class);
    $source = $source->reveal();

    $environment = new Environment('test');
    $environment->addHtmlParser($available);

    $exception = new MissingHtmlParserException(
      source: $source,
      environment: $environment,
    );

    self::assertSame($source, $exception->source);
    self::assertStringContainsString(
      $source::class,
      $exception->getMessage(),
    );
    self::assertStringContainsString(
      $available::class,
      $exception->getMessage(),
    );
  }

}
