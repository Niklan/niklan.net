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
use Drupal\niklan\Repository\KeyValue\LanguageAwareSettingsStore;

abstract class SettingsForm implements FormInterface, ContainerInjectionInterface {

  use DependencySerializationTrait;

  abstract protected function getMessenger(): MessengerInterface;

  abstract protected function getCacheTagsInvalidator(): CacheTagsInvalidatorInterface;

  abstract protected function getSettings(): LanguageAwareSettingsStore;

  abstract protected function doBuildForm(array &$form, FormStateInterface $form_state): void;

  abstract protected function doSubmitForm(array &$form, FormStateInterface $form_state): void;

  #[\Override]
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // Not need for an abstract.
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $this->doBuildForm($form, $form_state);

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
    $this->doSubmitForm($form, $form_state);

    $this
      ->getMessenger()
      ->addStatus(new TranslatableMarkup('Settings successfully saved.'));

    $this
      ->getCacheTagsInvalidator()
      ->invalidateTags($this->getSettings()->getCacheTags());
  }

}
