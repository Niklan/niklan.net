<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Unit\Repository;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\niklan\Repository\AboutSettingsRepository;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides a test for about settings repository.
 *
 * @coversDefaultClass \Drupal\niklan\Repository\AboutSettingsRepository
 */
final class AboutSettingsRepositoryTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * The array with prophecy store values.
   */
  protected array $storeValues = [];

  /**
   * The mocked key/value factory.
   */
  protected KeyValueFactoryInterface $keyValueFactory;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $self = $this;

    $key_value_store_prophecy = $this->prophesize(KeyValueStoreInterface::class);

    $key_value_store_prophecy->get(Argument::any(), Argument::any())->will(static function ($args) use ($self): mixed {
      return $self->storeValues[$args[0]] ?? NULL;
    });

    $key_value_store_prophecy->set(Argument::any(), Argument::any())->will(static function ($args) use ($self): void {
      $self->storeValues[$args[0]] = $args[1];
    });

    $key_value_store_prophecy->delete(Argument::any())->will(static function ($args) use ($self): void {
      unset($self->storeValues[$args[0]]);
    });

    $key_value_factory_prophecy = $this->prophesize(KeyValueFactoryInterface::class);
    $key_value_factory_prophecy->get(Argument::any())->willReturn($key_value_store_prophecy->reveal());

    $this->keyValueFactory = $key_value_factory_prophecy->reveal();
  }

  /**
   * Tests that repository works as expected.
   */
  public function testRepository(): void {
    $repository = new AboutSettingsRepository($this->keyValueFactory);

    $this->assertNull($repository->getPhotoMediaId());
    $this->assertNull($repository->getPhotoResponsiveImageStyleId());

    $repository->setPhotoMediaId('123');
    $this->assertEquals('123', $repository->getPhotoMediaId());
    $repository->setPhotoMediaId(NULL);
    $this->assertNull($repository->getPhotoMediaId());

    $repository->setPhotoResponsiveImageStyleId('image_style_name');
    $this->assertEquals('image_style_name', $repository->getPhotoResponsiveImageStyleId());
    $repository->setPhotoResponsiveImageStyleId(NULL);
    $this->assertNull($repository->getPhotoResponsiveImageStyleId());
  }

}
