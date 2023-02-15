<?php declare(strict_types = 1);

namespace Drupal\niklan\Hook\SymfonyMailer;

use Drupal\contact\Entity\Message;
use Drupal\symfony_mailer\EmailInterface;

/**
 * Provides additional 'contact_form' build processing.
 */
final class ContactFormEmailBuild {

  /**
   * Implements hook_mailer_TYPE_PHASE().
   */
  public function __invoke(EmailInterface $email): void {
    $this->setReplyTo($email);
  }

  /**
   * Sets 'Reply-To' header for an email.
   *
   * The sender emails collected by 'field_email' and hence, Symfony Mailer
   * knows nothing about it.
   *
   * @param \Drupal\symfony_mailer\EmailInterface $email
   *   The email.
   */
  protected function setReplyTo(EmailInterface $email): void {
    // Only set it for 'mail' subtype.
    if ($email->getSubType() !== 'mail') {
      return;
    }

    $contact_message = $email->getParam('contact_message');
    \assert($contact_message instanceof Message);

    if (!$contact_message->hasField('field_email') || $contact_message->get('field_email')->isEmpty()) {
      return;
    }

    $email->setReplyTo($contact_message->get('field_email')->getString());
  }

}
