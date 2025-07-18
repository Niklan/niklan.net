<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Deploy;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\contact\MessageInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a deployment #0001.
 *
 * @see niklan_deploy_0001()
 */
final class Deploy0001 implements ContainerInjectionInterface {

  /**
   * Constructs a new Deploy0001 instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Prepares variables for batch if they are not initialized.
   *
   * @param array $sandbox
   *   The batch sandbox.
   */
  protected function prepareBatch(array &$sandbox): void {
    if (isset($sandbox['total'])) {
      return;
    }

    $sandbox['total'] = $this->getContactMessageQuery()->count()->execute();
    $sandbox['current'] = 0;
    $sandbox['limit'] = Settings::get('entity_update_batch_size', 50);
  }

  /**
   * Process a single batch.
   *
   * @param array $sandbox
   *   The batch sandbox.
   */
  protected function processBatch(array &$sandbox): void {
    $ids = $this
      ->getContactMessageQuery()
      ->range($sandbox['current'], $sandbox['limit'])
      ->execute();
    \assert(\is_array($ids));

    $contact_messages = $this
      ->entityTypeManager
      ->getStorage('contact_message')
      ->loadMultiple($ids);

    foreach ($contact_messages as $contact_message) {
      \assert($contact_message instanceof MessageInterface);

      if (!$contact_message->get('field_email')->isEmpty()) {
        $email = $contact_message->get('field_email')->getString();
        $contact_message->setSenderMail($email);
      }

      if (!$contact_message->get('field_fulltext_unformatted')->isEmpty()) {
        $message = $contact_message
          ->get('field_fulltext_unformatted')
          ->getString();
        $contact_message->setMessage($message);
      }

      $contact_message->save();

      $sandbox['current']++;
    }

    $sandbox['#finished'] = $sandbox['current'] / $sandbox['total'];
  }

  /**
   * Builds a default query for update.
   */
  protected function getContactMessageQuery(): QueryInterface {
    return $this
      ->entityTypeManager
      ->getStorage('contact_message')
      ->getQuery()
      ->accessCheck(FALSE)
      ->sort('id');
  }

  /**
   * Implements hook_deploy_HOOK().
   */
  public function __invoke(array &$sandbox): string {
    $this->prepareBatch($sandbox);

    if ($sandbox['total'] === 0) {
      $sandbox['#finished'] = 1;

      return 'No contact messages found.';
    }

    $this->processBatch($sandbox);

    return (string) new FormattableMarkup('@current of @total contact message processed.', [
      '@current' => $sandbox['current'],
      '@total' => $sandbox['total'],
    ]);
  }

}
