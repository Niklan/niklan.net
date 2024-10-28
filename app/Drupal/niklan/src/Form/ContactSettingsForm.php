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
use Drupal\niklan\Repository\ContactSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContactSettingsForm implements FormInterface, ContainerInjectionInterface {

  use DependencySerializationTrait;

  public function __construct(
    private readonly ContactSettings $settings,
    private readonly MessengerInterface $messenger,
    private readonly CacheTagsInvalidatorInterface $cacheTagsInvalidator,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(ContactSettings::class),
      $container->get(MessengerInterface::class),
      $container->get(CacheTagsInvalidatorInterface::class),
    );
  }

  #[\Override]
  public function getFormId(): string {
    return 'niklan_contact_settings_form';
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#tree'] = TRUE;

    $form['email'] = [
      '#type' => 'email',
      '#title' => new TranslatableMarkup('Email'),
      '#default_value' => $this->settings->getEmail(),
      '#required' => TRUE,
    ];

    $form['telegram'] = [
      '#type' => 'url',
      '#title' => new TranslatableMarkup('Telegram'),
      '#default_value' => $this->settings->getTelegram(),
      '#required' => TRUE,
    ];

    $form['description'] = [
      '#type' => 'text_format',
      '#title' => new TranslatableMarkup('Description'),
      '#description' => new TranslatableMarkup('The description of the about page.'),
      '#default_value' => $this->settings->getDescription(),
      '#allowed_formats' => [ContactSettings::TEXT_FORMAT],
      '#rows' => 3,
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
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // Not needed.
  }

  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this
      ->settings
      ->setEmail($form_state->getValue('email'))
      ->setTelegram($form_state->getValue('telegram'))
      ->setDescription($form_state->getValue(['description', 'value']));

    $this
      ->messenger
      ->addStatus(new TranslatableMarkup('Settings successfully saved.'));

    $this
      ->cacheTagsInvalidator
      ->invalidateTags($this->settings->getCacheTags());
  }

}
