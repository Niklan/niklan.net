<?php

declare(strict_types=1);

namespace Drupal\niklan\Form;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Repository\ServicesSettings;
use Drupal\niklan\Repository\SupportSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ServicesSettingsForm implements FormInterface, ContainerInjectionInterface {

  use DependencySerializationTrait;

  public function __construct(
    private readonly ServicesSettings $settings,
    private readonly MessengerInterface $messenger,
    private readonly CacheTagsInvalidatorInterface $cacheTagsInvalidator,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(ServicesSettings::class),
      $container->get(MessengerInterface::class),
      $container->get(CacheTagsInvalidatorInterface::class),
    );
  }

  #[\Override]
  public function getFormId(): string {
    return 'niklan_services_settings';
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#tree'] = TRUE;

    $form['description'] = [
      '#type' => 'text_format',
      '#title' => new TranslatableMarkup('Body'),
      '#description' => new TranslatableMarkup('The description of service page.'),
      '#default_value' => $this->settings->getDescription(),
      '#allowed_formats' => [SupportSettings::TEXT_FORMAT],
      '#rows' => 3,
      '#required' => TRUE,
    ];

    $form['hourly_rate'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Hourly rate'),
      '#default_value' => $this->settings->getHourlyRate(),
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['save'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this
      ->settings
      ->setDescription($form_state->getValue(['description', 'value']))
      ->setHourlyRate($form_state->getValue(['hourly_rate']));

    $this
      ->messenger
      ->addStatus(new TranslatableMarkup('Settings successfully saved.'));

    $this
      ->cacheTagsInvalidator
      ->invalidateTags($this->settings->getCacheTags());
  }

  #[\Override]
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // Not needed.
  }

}
