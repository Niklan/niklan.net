<?php

declare(strict_types=1);

namespace Drupal\niklan\Form\Settings;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Repository\KeyValue\LanguageAwareSettingsStore;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @see \Drupal\niklan\Repository\KeyValue\LanguageAwareSettingsRoutes
 */
abstract class SettingsForm implements FormInterface, ContainerInjectionInterface {

  use DependencySerializationTrait;

  protected ?LanguageAwareSettingsStore $settings = NULL;

  abstract protected function loadSettings(): LanguageAwareSettingsStore;

  final public function __construct(
    protected ContainerInterface $container,
    protected MessengerInterface $messenger,
    protected CacheTagsInvalidatorInterface $cacheTagsInvalidator,
    protected RouteMatchInterface $routeMatch,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get(ContainerInterface::class),
      $container->get(MessengerInterface::class),
      $container->get(CacheTagsInvalidatorInterface::class),
      $container->get(RouteMatchInterface::class),
    );
  }

  #[\Override]
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // Not need for an abstract.
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $language_code = $this
      ->getRouteMatch()
      ->getParameter('key_value_language_aware_code');
    $this->getSettings()->changeLanguageCode($language_code);

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

  protected function getRouteMatch(): RouteMatchInterface {
    return $this->routeMatch;
  }

  protected function getMessenger(): MessengerInterface {
    return $this->messenger;
  }

  protected function getCacheTagsInvalidator(): CacheTagsInvalidatorInterface {
    return $this->cacheTagsInvalidator;
  }

  protected function getSettings(): LanguageAwareSettingsStore {
    if ($this->settings) {
      return $this->settings;
    }

    $this->settings = $this->loadSettings();

    return $this->settings;
  }

  protected function getContainer(): ContainerInterface {
    return $this->container;
  }

}
