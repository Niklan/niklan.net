<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Extension;

use Drupal\external_content\Builder\ElementRenderArrayBuilder;
use Drupal\external_content\Builder\PlainTextRenderArrayBuilder;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\external_content\Serializer\ElementSerializer;
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
      static fn (SerializerInterface $serializer) => $serializer::class,
      $environment->getSerializers()->getIterator()->getArrayCopy(),
    );
    self::assertContains(ElementSerializer::class, $serializer_classes);
    self::assertContains(PlainTextSerializer::class, $serializer_classes);

    self::assertCount(1, $environment->getHtmlParsers());
    self::assertCount(2, $environment->getRenderArrayBuilders());
    $builder_classes = \array_map(
      static fn (RenderArrayBuilderInterface $builder) => $builder::class,
      $environment->getRenderArrayBuilders()->getIterator()->getArrayCopy(),
    );
    self::assertContains(ElementRenderArrayBuilder::class, $builder_classes);
    self::assertContains(PlainTextRenderArrayBuilder::class, $builder_classes);
  }

}
