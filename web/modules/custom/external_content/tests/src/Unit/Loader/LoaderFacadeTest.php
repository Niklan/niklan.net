<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Loader;

use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Loader\LoaderResultEntityInterface;
use Drupal\external_content\Data\ExternalContentBundleDocument;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\LoaderResult;
use Drupal\external_content\Data\LoaderResultIgnore;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Loader\LoaderFacade;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Loader\LoaderFacade
 * @group external_content
 */
final class LoaderFacadeTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testLoadWithValidLoaders(): void {
    $environment = new Environment();
    // Make sure it has higher priority.
    $environment->addLoader($this->prepareSkipLoader(), 1000);
    $environment->addLoader($this->prepareSuccessLoader());

    $loader = new LoaderFacade();
    $loader->setEnvironment($environment);

    $result = $loader->load($this->prepareExternalContentBundleDocument());

    self::assertInstanceOf(LoaderResultEntityInterface::class, $result);
    self::assertSame('foo', $result->getEntityTypeId());
    self::assertSame('bar', $result->getEntityId());
  }

  /**
   * {@selfdoc}
   */
  public function testLoadWithoutLoaders(): void {
    $environment = new Environment();

    $loader = new LoaderFacade();
    $loader->setEnvironment($environment);

    $result = $loader->load($this->prepareExternalContentBundleDocument());

    self::assertInstanceOf(LoaderResultIgnore::class, $result);
  }

  /**
   * {@selfdoc}
   */
  private function prepareExternalContentBundleDocument(): ExternalContentBundleDocument {
    $file = new ExternalContentFile('foo', 'bar');
    $document = new ExternalContentDocument($file);

    return new ExternalContentBundleDocument($document);
  }

  /**
   * {@selfdoc}
   */
  private function prepareSkipLoader(): LoaderInterface {
    $result = LoaderResult::skip();

    $loader = $this->prophesize(LoaderInterface::class);
    $loader->load(Argument::cetera())->willReturn($result);

    return $loader->reveal();
  }

  /**
   * {@selfdoc}
   */
  private function prepareSuccessLoader(): LoaderInterface {
    $result = LoaderResult::entity('foo', 'bar');

    $loader = $this->prophesize(LoaderInterface::class);
    $loader->load(Argument::cetera())->willReturn($result);

    return $loader->reveal();
  }

}
