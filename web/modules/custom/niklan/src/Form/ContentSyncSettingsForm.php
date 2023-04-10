<?php declare(strict_types = 1);

namespace Drupal\niklan\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Repository\ContentSyncSettingsRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form with content synchronization settings.
 *
 * @ingroup content_sync
 */
final class ContentSyncSettingsForm extends FormBase {

  /**
   * Constructs a new ContentSyncSettingsForm instance.
   *
   * @param \Drupal\niklan\Repository\ContentSyncSettingsRepositoryInterface $contentSyncSettings
   *   The content sync settings.
   */
  public function __construct(
    protected ContentSyncSettingsRepositoryInterface $contentSyncSettings,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('niklan.repository.content_sync_settings'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'niklan_content_sync_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['working_dir'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Working directory'),
      '#description' => new TranslatableMarkup('Directory URI where content source is located. Should be a git directory.'),
      '#default_value' => $this->contentSyncSettings->getWorkingDir(),
      '#attributes' => [
        'placeholder' => 'private://content',
      ],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $working_dir = $form_state->getValue('working_dir');
    $this
      ->contentSyncSettings
      ->setWorkingDir($working_dir ?: NULL);
  }

}
