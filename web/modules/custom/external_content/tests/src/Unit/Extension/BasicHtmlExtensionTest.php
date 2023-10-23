<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Extension;

use Drupal\external_content\Builder\HtmlElementBuilder;
use Drupal\external_content\Builder\PlainTextBuilder;
use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Parser\ParserInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
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
    $serializer_classes = \array_map(
      static fn (NodeSerializerInterface $serializer) => $serializer::class,
      $environment->getSerializers()->getIterator()->getArrayCopy(),
    );
    self::assertContains(
      ExternalContentDocumentSerializer::class,
      $serializer_classes,
    );
    self::assertContains(HtmlElementSerializer::class, $serializer_classes);
    self::assertContains(PlainTextSerializer::class, $serializer_classes);

    self::assertCount(2, $environment->getParsers());
    $parser_classes = \array_map(
      static fn (ParserInterface $parser) => $parser::class,
      $environment->getParsers()->getIterator()->getArrayCopy(),
    );
    self::assertContains(HtmlElementParser::class, $parser_classes);
    self::assertContains(PlainTextParser::class, $parser_classes);

    self::assertCount(2, $environment->getBuilders());
    $builder_classes = \array_map(
       static fn (BuilderInterface $builder) => $builder::class,
       $environment->getBuilders()->getIterator()->getArrayCopy(),
     );
    self::assertContains(HtmlElementBuilder::class, $builder_classes);
    self::assertContains(PlainTextBuilder::class, $builder_classes);
  }

}
