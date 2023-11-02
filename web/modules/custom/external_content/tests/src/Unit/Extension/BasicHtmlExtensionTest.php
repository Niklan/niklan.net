<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Extension;

use Drupal\external_content\Builder\Html\ElementRenderArrayBuilder;
use Drupal\external_content\Builder\Html\PlainTextRenderArrayBuilder;
use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Parser\ParserInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\external_content\Parser\Html\ElementParser;
use Drupal\external_content\Parser\Html\PlainTextParser;
use Drupal\external_content\Serializer\ElementSerializer;
use Drupal\external_content\Serializer\ExternalContentDocumentSerializer;
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

    self::assertCount(2, $environment->getSerializers());
    $serializer_classes = \array_map(
      static fn (NodeSerializerInterface $serializer) => $serializer::class,
      $environment->getSerializers()->getIterator()->getArrayCopy(),
    );
    self::assertContains(
      ExternalContentDocumentSerializer::class,
      $serializer_classes,
    );
    self::assertContains(ElementSerializer::class, $serializer_classes);
    self::assertContains(PlainTextSerializer::class, $serializer_classes);

    self::assertCount(1, $environment->getParsers());
    $parser_classes = \array_map(
      static fn (ParserInterface $parser) => $parser::class,
      $environment->getParsers()->getIterator()->getArrayCopy(),
    );
    self::assertContains(ElementParser::class, $parser_classes);
    self::assertContains(PlainTextParser::class, $parser_classes);

    self::assertCount(1, $environment->getBuilders());
    $builder_classes = \array_map(
       static fn (BuilderInterface $builder) => $builder::class,
       $environment->getBuilders()->getIterator()->getArrayCopy(),
     );
    self::assertContains(ElementRenderArrayBuilder::class, $builder_classes);
    self::assertContains(PlainTextRenderArrayBuilder::class, $builder_classes);
  }

}
