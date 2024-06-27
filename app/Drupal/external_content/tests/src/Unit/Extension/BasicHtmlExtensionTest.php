<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Extension;

use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderManagerInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Contract\Parser\HtmlParserManagerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerManagerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\Tests\UnitTestCaseTest;
use Prophecy\Argument;

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
    $manager = $this->prophesize(ExternalContentManagerInterface::class);

    $serializer = $this->prophesize(SerializerInterface::class);
    $serializer = $serializer->reveal();
    $serializer_manager = $this->prophesize(SerializerManagerInterface::class);
    $serializer_manager->get(Argument::any())->willReturn($serializer);
    $manager
      ->getSerializerManager()
      ->willReturn($serializer_manager->reveal())
      ->shouldBeCalledOnce();

    $parser = $this->prophesize(HtmlParserInterface::class);
    $parser = $parser->reveal();
    $parser_manager = $this->prophesize(HtmlParserManagerInterface::class);
    $parser_manager->get(Argument::any())->willReturn($parser);
    $manager
      ->getHtmlParserManager()
      ->willReturn($parser_manager->reveal())
      ->shouldBeCalledOnce();

    $builder = $this->prophesize(RenderArrayBuilderInterface::class);
    $builder = $builder->reveal();
    $builder_manager = $this->prophesize(RenderArrayBuilderManagerInterface::class);
    $builder_manager->get(Argument::any())->willReturn($builder);
    $manager
      ->getRenderArrayBuilderManager()
      ->willReturn($builder_manager->reveal())
      ->shouldBeCalledOnce();

    $manager = $manager->reveal();

    $extension = new BasicHtmlExtension($manager);

    $environment = new Environment('test');
    $environment->addExtension($extension);

    self::assertCount(3, $environment->getHtmlParsers());
    self::assertCount(4, $environment->getRenderArrayBuilders());
    self::assertCount(4, $environment->getSerializers());
  }

}
