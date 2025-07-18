<?php

declare(strict_types=1);

namespace Drupal\niklan\LanguageAwareStore\Form;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\LanguageAwareStore\Repository\LanguageAwareSettingsStore;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @see \Drupal\niklan\LanguageAwareStore\EventSubscriber\LanguageAwareSettingsRoutes
 */
abstract class LanguageAwareStoreForm implements FormInterface, ContainerInjectionInterface {

  use DependencySerializationTrait;

  abstract protected function getSettings(): LanguageAwareSettingsStore;

  final public function __construct(
    protected ContainerInterface $container,
    protected MessengerInterface $messenger,
    protected CacheTagsInvalidatorInterface $cacheTagsInvalidator,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new static(
      $container,
      $container->get(MessengerInterface::class),
      $container->get(CacheTagsInvalidatorInterface::class),
    );
  }

  #[\Override]
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // Not need for an abstract.
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
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
      ->getMessenger()
      ->addStatus(new TranslatableMarkup('Settings successfully saved.'));

    $this
      ->getCacheTagsInvalidator()
      ->invalidateTags($this->getSettings()->getCacheTags());
  }

  protected function getMessenger(): MessengerInterface {
    return $this->messenger;
  }

  protected function getCacheTagsInvalidator(): CacheTagsInvalidatorInterface {
    return $this->cacheTagsInvalidator;
  }

  protected function getContainer(): ContainerInterface {
    return $this->container;
  }

}
