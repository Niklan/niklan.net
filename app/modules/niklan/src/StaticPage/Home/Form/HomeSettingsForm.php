<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\Home\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\app_contract\LanguageAwareStore\LanguageAwareStoreForm;
use Drupal\niklan\StaticPage\Home\Repository\HomeSettings;
use Drupal\app_platform\Utils\AjaxFormHelper;

final class HomeSettingsForm extends LanguageAwareStoreForm {

  #[\Override]
  public function getFormId(): string {
    return 'niklan_home_settings_form';
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
    // Workaround for core bug #2897377.
    $form['#id'] = 'home-settings-form';
    $form['#tree'] = TRUE;

    $form = parent::buildForm($form, $form_state);

    $form['heading'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Heading'),
      '#default_value' => $this->getSettings()->getHeading(),
      '#required' => TRUE,
    ];

    $form['description'] = [
      '#type' => 'text_format',
      '#title' => new TranslatableMarkup('Description'),
      '#description' => new TranslatableMarkup('The description of the home page.'),
      '#default_value' => $this->getSettings()->getDescription(),
      '#allowed_formats' => [HomeSettings::TEXT_FORMAT],
      '#rows' => 3,
      '#required' => TRUE,
    ];

    $this->buildCards($form, $form_state);

    return $form;
  }

  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $card_items = $form_state->getValue(['cards', 'items'], []);
    \assert(\is_array($card_items));
    $cards = [];

    /**
     * @var array{
     *   media_id: string,
     *   title: string,
     *   description: string
     * } $card_item
     */
    foreach ($card_items as $card_item) {
      $cards[] = [
        'media_id' => $card_item['media_id'],
        'title' => $card_item['title'],
        'description' => $card_item['description'],
      ];
    }

    $settings = $this->getSettings();

    \assert(\is_string($form_state->getValue('heading')));
    $settings->setHeading($form_state->getValue('heading'));
    \assert(\is_string($form_state->getValue(['description', 'value'])));
    $settings->setDescription($form_state->getValue(['description', 'value']));
    $settings->setCards($cards);

    parent::submitForm($form, $form_state);
  }

  public static function addCard(array &$form, FormStateInterface $form_state): void {
    $cards_count = $form_state->get('cards_count');
    \assert(\is_int($cards_count));
    $form_state->set('cards_count', $cards_count + 1);
    $form_state->set('keep_cards_open', TRUE);
    $form_state->setRebuild();
  }

  public static function removeCard(array &$form, FormStateInterface $form_state): void {
    $form_state->set('keep_cards_open', TRUE);

    $button = $form_state->getTriggeringElement();
    // This is row delta during build.
    $delta = (string) ($button['#delta'] ?? 0);
    $input = $form_state->getUserInput();
    $rows = &NestedArray::getValue(
      array: $input,
      // This should be '#parents', because we're working with array of values,
      // not the form tree.
      // @phpstan-ignore-next-line offsetAccess.notFound
      parents: \array_slice($button['#parents'], 0, -1),
    );
    \assert(\is_array($rows));
    // Rows can be sorted by weight and indexes are not always in ASC order.
    unset($rows[$delta]);
    $rows = \array_values($rows);

    $cards_count = $form_state->get('cards_count');
    \assert(\is_int($cards_count));

    if ($cards_count > 0) {
      $form_state->set('cards_count', $cards_count - 1);
    }

    $form_state->setRebuild();
  }

  #[\Override]
  protected function getSettings(): HomeSettings {
    return $this->getContainer()->get(HomeSettings::class);
  }

  private function buildCards(array &$form, FormStateInterface $form_state): void {
    $cards = $this->getSettings()->getCards();
    $cards_count = $form_state->get('cards_count');

    if (!isset($cards_count)) {
      $cards_count = \count($cards);
      $form_state->set('cards_count', $cards_count);
    }

    $form['cards'] = [
      '#type' => 'details',
      '#open' => $form_state->get('keep_cards_open') ?? FALSE,
      '#title' => new TranslatableMarkup('Cards'),
      '#prefix' => '<div id="cards-wrapper">',
      '#suffix' => '</div>',
    ];

    $items = [
      '#type' => 'table',
      // Workaround for an empty string if not set. See #3247373.
      '#input' => FALSE,
      '#header' => [
        'content' => new TranslatableMarkup('Content'),
        'operations' => new TranslatableMarkup('Operations'),
        '_weight' => new TranslatableMarkup('Weight'),
      ],
      '#empty' => new TranslatableMarkup('No cards has been added yet.'),
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'weight',
          'hidden' => TRUE,
        ],
      ],
    ];

    for ($i = 0; $i < $cards_count; $i++) {
      $card_data = $cards[$i] ?? [];

      /**
       * @var array{
       *   '#attributes': array{'class': array<string>},
       *   '#weight': int,
       *   'content': array,
       *   'operations': array,
       *   '_weight': array
       * } $row
       */
      $row = [
        '#attributes' => ['class' => ['draggable']],
        '#weight' => $i,
        'content' => $this->buildSingleCard($card_data),
        'operations' => [
          'delete' => [
            '#id' => "cards-delete-card-{$i}",
            '#name' => "cards_delete_card_{$i}",
            '#delta' => $i,
            '#type' => 'submit',
            '#submit' => [[self::class, 'removeCard']],
            '#value' => new TranslatableMarkup('Delete'),
            '#attributes' => ['class' => ['button--small', 'button--danger']],
            '#validate' => [],
            '#limit_validation_errors' => [],
            '#ajax' => [
              'callback' => [AjaxFormHelper::class, 'refresh'],
            ],
            '#parents' => ['cards', 'items', $i],
          ],
        ],
        '_weight' => [
          '#type' => 'weight',
          '#title_display' => 'invisible',
          '#default_value' => $i,
          '#attributes' => ['class' => ['weight']],
        ],
      ];

      $items[$i] = $row;
    }

    $form['cards']['items'] = $items;
    $form['cards']['actions'] = ['#type' => 'actions'];
    $form['cards']['actions']['add'] = [
      // Workaround for core bug #2897377.
      '#id' => 'cards-add-card',
      '#name' => 'cards_actions_add_button',
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Add card'),
      '#submit' => [[self::class, 'addCard']],
      '#limit_validation_errors' => [],
      '#ajax' => [
        'callback' => [AjaxFormHelper::class, 'refresh'],
      ],
      '#parents' => ['cards', 'actions'],
      '#attributes' => ['class' => ['button--small']],
    ];
  }

  private function buildSingleCard(array $card_data): array {
    $card = [];

    $card['media_id'] = [
      '#type' => 'media_library',
      '#allowed_bundles' => ['image'],
      '#title' => new TranslatableMarkup('Background'),
      '#default_value' => $card_data['media_id'] ?? NULL,
      '#required' => TRUE,
    ];

    $card['title'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => new TranslatableMarkup('Title'),
      '#default_value' => $card_data['title'] ?? NULL,
    ];

    $card['description'] = [
      '#type' => 'text_format',
      '#title' => new TranslatableMarkup('Description'),
      '#default_value' => $card_data['description'] ?? NULL,
      '#allowed_formats' => [HomeSettings::TEXT_FORMAT],
      '#rows' => 1,
      '#required' => TRUE,
    ];

    return $card;
  }

}
