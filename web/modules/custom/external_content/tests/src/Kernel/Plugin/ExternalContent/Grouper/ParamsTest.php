<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Plugin\ExternalContent\Grouper;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\external_content\Dto\ExternalContent;
use Drupal\external_content\Dto\ParsedSourceFile;
use Drupal\external_content\Dto\ParsedSourceFileCollection;
use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileContent;
use Drupal\external_content\Dto\SourceFileParams;
use Drupal\external_content\Plugin\ExternalContent\Grouper\GrouperPluginManagerInterface;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use Prophecy\Argument;

/**
 * Provides a test for 'params' grouper plugin.
 *
 * @coversDefaultClass \Drupal\external_content\Plugin\ExternalContent\Grouper\Params
 */
final class ParamsTest extends ExternalContentTestBase {

  /**
   * The grouper plugin manager.
   */
  protected ?GrouperPluginManagerInterface $grouperPluginManager = NULL;

  /**
   * Tests that plugin works as expected.
   */
  public function testPlugin(): void {
    $file_a = new ParsedSourceFile(
      new SourceFile('', ''),
      new SourceFileParams(['id' => 'a']),
      new SourceFileContent(),
    );

    $file_b = new ParsedSourceFile(
      new SourceFile('', ''),
      new SourceFileParams(['id' => 'b', 'language' => 'en']),
      new SourceFileContent(),
    );

    $file_c = new ParsedSourceFile(
      new SourceFile('', ''),
      new SourceFileParams(['id' => 'a', 'language' => 'ru']),
      new SourceFileContent(),
    );

    $file_collection = new ParsedSourceFileCollection();
    $file_collection->add($file_a);
    $file_collection->add($file_b);
    $file_collection->add($file_c);

    /** @var \Drupal\external_content\Plugin\ExternalContent\Grouper\Params $plugin */
    $plugin = $this->grouperPluginManager->createInstance('params');
    $external_content_collection = $plugin->group($file_collection);

    self::assertCount(2, $external_content_collection);
    self::assertTrue($external_content_collection->has('a'));
    self::assertTrue($external_content_collection->has('b'));
    self::assertFalse($external_content_collection->has('c'));

    $content_a = $external_content_collection->get('a');
    self::assertInstanceOf(ExternalContent::class, $content_a);
    self::assertEquals('a', $content_a->id());
    self::assertTrue($content_a->hasTranslation('en'));
    self::assertTrue($content_a->hasTranslation('ru'));
    self::assertSame($file_a, $content_a->getTranslation('en'));
    self::assertSame($file_c, $content_a->getTranslation('ru'));

    $content_b = $external_content_collection->get('b');
    self::assertEquals('b', $content_b->id());
    self::assertTrue($content_b->hasTranslation('en'));
    self::assertFalse($content_b->hasTranslation('ru'));
    self::assertSame($file_b, $content_b->getTranslation('en'));
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $default_language = $this->prophesize(LanguageInterface::class);
    $default_language->getId()->willReturn('en');

    $language_manager = $this->prophesize(LanguageManagerInterface::class);
    $language_manager->getDefaultLanguage(Argument::any())
      ->willReturn($default_language->reveal());

    $this->container->set('language_manager', $language_manager->reveal());
    $this->grouperPluginManager = $this->container->get(
      GrouperPluginManagerInterface::class,
    );
  }

}
