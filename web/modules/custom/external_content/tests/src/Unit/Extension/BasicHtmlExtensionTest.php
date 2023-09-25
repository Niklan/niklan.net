<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Extension;

use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\external_content\Parser\HtmlElementParser;
use Drupal\external_content\Parser\PlainTextParser;
use Drupal\external_content\Serializer\ExternalContentDocumentSerializer;
use Drupal\external_content\Serializer\HtmlElementSerializer;
use Drupal\external_content\Serializer\PlainTextSerializer;
use Drupal\Tests\UnitTestCaseTest;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Extension\BasicHtmlExtension
 * @group external_content
 */
final class BasicHtmlExtensionTest extends UnitTestCaseTest {

  /**
   * {@selfdoc}
   */
  public function testExtension(): void {
    $environment = new Environment();
    $environment->addExtension(new BasicHtmlExtension());

    self::assertCount(3, $environment->getSerializers());
    self::assertContains(
      ExternalContentDocumentSerializer::class,
      $environment->getSerializers(),
    );
    self::assertContains(
      HtmlElementSerializer::class,
      $environment->getSerializers(),
    );
    self::assertContains(
      PlainTextSerializer::class,
      $environment->getSerializers(),
    );

    self::assertCount(2, $environment->getHtmlParsers());
    self::assertContains(
      HtmlElementParser::class,
      $environment->getHtmlParsers(),
    );
    self::assertContains(
      PlainTextParser::class,
      $environment->getHtmlParsers(),
    );

    // @todo Test builders when implemented.
    //   self::assertCount(2, $environment->getBuilders());
  }

}
