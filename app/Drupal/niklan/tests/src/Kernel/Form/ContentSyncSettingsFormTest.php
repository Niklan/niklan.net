<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Form;

use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\niklan\Form\ContentSyncSettingsForm;
use Drupal\niklan\Repository\ContentSyncSettingsRepositoryInterface;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;

/**
 * Provides a test for content sync settings form.
 *
 * @covers \Drupal\niklan\Form\ContentSyncSettingsForm
 */
final class ContentSyncSettingsFormTest extends NiklanTestBase {

  /**
   * The form builder.
   */
  protected FormBuilderInterface $formBuilder;

  /**
   * The content sync settings.
   */
  protected ContentSyncSettingsRepositoryInterface $contentSyncSettings;

  /**
   * Tests that settings works as expected.
   */
  public function testSettings(): void {
    self::assertNull($this->contentSyncSettings->getWorkingDir());

    // Submit an empty form.
    $form_state = new FormState();
    $this->formBuilder->submitForm(ContentSyncSettingsForm::class, $form_state);

    self::assertNull($this->contentSyncSettings->getWorkingDir());

    // Submit with settings.
    $working_dir = 'private://content';
    $form_state->setValue('working_dir', $working_dir);
    $this->formBuilder->submitForm(ContentSyncSettingsForm::class, $form_state);

    self::assertEquals(
      $working_dir,
      $this->contentSyncSettings->getWorkingDir(),
    );

    // Submit a default form.
    $form_state->cleanValues();
    $this->formBuilder->submitForm(ContentSyncSettingsForm::class, $form_state);

    self::assertEquals(
      $working_dir,
      $this->contentSyncSettings->getWorkingDir(),
    );

    // Submit with empty values.
    $form_state->setValue('working_dir', '');
    $this->formBuilder->submitForm(ContentSyncSettingsForm::class, $form_state);

    self::assertNull($this->contentSyncSettings->getWorkingDir());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->formBuilder = $this->container->get('form_builder');
    $this->contentSyncSettings = $this
      ->container
      ->get('niklan.repository.content_sync_settings');
  }

}
