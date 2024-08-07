<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Functional\Form;

use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\media\MediaInterface;
use Drupal\niklan\Form\AboutSettingsForm;
use Drupal\niklan\Repository\AboutSettingsRepositoryInterface;
use Drupal\responsive_image\Entity\ResponsiveImageStyle;
use Drupal\Tests\media\Traits\MediaTypeCreationTrait;
use Drupal\Tests\niklan\Functional\NiklanTestBase;
use Drupal\Tests\TestFileCreationTrait;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides a test for about settings form.
 *
 * @coversDefaultClass \Drupal\niklan\Form\AboutSettingsForm
 */
final class AboutSettingsFormTest extends NiklanTestBase {

  use ProphecyTrait;
  use MediaTypeCreationTrait;
  use TestFileCreationTrait;

  /**
   * The form builder.
   */
  protected ?FormBuilderInterface $formBuilder;

  /**
   * The about settings repository.
   */
  protected ?AboutSettingsRepositoryInterface $settings;

  /**
   * Tests that form works as expected.
   */
  public function testForm(): void {
    self::assertNull($this->settings->getPhotoMediaId());
    self::assertNull($this->settings->getPhotoResponsiveImageStyleId());

    $form_state = new FormState();
    $form_state->setValues([
      'photo' => [
        'media_id' => 'Test (1)',
        'responsive_image_style' => 'foo',
      ],
    ]);
    $this->formBuilder->submitForm(AboutSettingsForm::class, $form_state);

    self::assertCount(0, $form_state->getErrors());
    self::assertEquals('1', $this->settings->getPhotoMediaId());
    self::assertEquals(
      'foo',
      $this->settings->getPhotoResponsiveImageStyleId(),
    );

    $form = $this
      ->formBuilder
      ->buildForm(AboutSettingsForm::class, $form_state);

    $photo_media_id_default_value = $form['photo']['media_id']['#default_value'];
    self::assertInstanceOf(
      MediaInterface::class,
      $photo_media_id_default_value,
    );
    self::assertEquals('1', $photo_media_id_default_value->id());
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $this->formBuilder = $this->container->get('form_builder');
    $this->settings = $this
      ->container
      ->get('niklan.repository.about_settings');

    $this->createMediaType('image', ['id' => 'image']);
    $file = File::create([
      'uri' => $this->getTestFiles('image')[0]->uri,
    ]);
    $file->save();
    $media_image = Media::create([
      'bundle' => 'image',
      'name' => 'Test',
      'field_media_image' => [
        [
          'target_id' => $file->id(),
          'alt' => 'Foo',
          'title' => 'Bar',
        ],
      ],
    ]);
    $media_image->save();

    $responsive_image_style = ResponsiveImageStyle::create([
      'id' => 'foo',
      'label' => 'Foo style',
    ]);
    $responsive_image_style->save();
  }

}
