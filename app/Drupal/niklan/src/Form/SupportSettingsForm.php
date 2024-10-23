<?php

declare(strict_types=1);

namespace Drupal\niklan\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Contract\Repository\SupportSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class SupportSettingsForm implements FormInterface, ContainerInjectionInterface {

  use DependencySerializationTrait;

  public function __construct(
    private SupportSettings $settings,
    private MessengerInterface $messenger,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(SupportSettings::class),
      $container->get(MessengerInterface::class),
    );
  }

  #[\Override]
  public function getFormId(): string {
    return 'niklan_support_settings';
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#tree'] = TRUE;

    $form['body'] = [
      '#type' => 'text_format',
      '#title' => new TranslatableMarkup('Body'),
      '#description' => new TranslatableMarkup('The body of the support page.'),
      '#default_value' => $this->settings->getBody(),
      '#allowed_formats' => [SupportSettings::TEXT_FORMAT],
      '#rows' => 3,
      '#required' => TRUE,
    ];

    $form['donate_url'] = [
      '#type' => 'url',
      '#title' => new TranslatableMarkup('Donate URL'),
      '#description' => new TranslatableMarkup('The URL of the donate page.'),
      '#default_value' => $this->settings->getDonateUrl(),
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
      ->setBody($form_state->getValue(['body', 'value']))
      ->setDonateUrl($form_state->getValue(['donate_url']));

    $this
      ->messenger
      ->addStatus(new TranslatableMarkup('Settings successfully saved.'));
  }

  #[\Override]
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // Not needed.
  }

}
