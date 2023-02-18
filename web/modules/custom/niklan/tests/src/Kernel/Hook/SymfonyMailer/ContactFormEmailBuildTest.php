<?php declare(strict_types = 1);

namespace Drupal\Tests\Kernel\Hook\SymfonyMailer;

use Drupal\contact\MessageInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\niklan\Hook\SymfonyMailer\ContactFormEmailBuild;
use Drupal\symfony_mailer\EmailInterface;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Prophecy\Argument;
use Prophecy\Argument\ArgumentsWildcard;
use Prophecy\Argument\Token\AnyValuesToken;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Tests hook_mailer_TYPE_PHASE() implementation.
 *
 * @coversDefaultClass \Drupal\niklan\Hook\Theme\LibraryInfoAlter
 */
final class ContactFormEmailBuildTest extends NiklanTestBase {

  use ProphecyTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['contact', 'symfony_mailer'];

  /**
   * Tests that 'Reply-To' mail header is not changed for other emails.
   */
  public function testSetReplyToSkip(): void {
    $email = $this->buildEmail('foo', NULL);

    $implementation = new ContactFormEmailBuild();
    $implementation($email->reveal());

    $calls = $email->findProphecyMethodCalls('setReplyTo', new ArgumentsWildcard([new AnyValuesToken()]),);
    self::assertCount(0, $calls);
  }

  /**
   * Builds an email prophecy.
   *
   * @param string $subtype
   *   The email subtype.
   * @param string|null $field_email_value
   *   The 'field_email' value from 'contact_message' entity.
   *
   * @return \Prophecy\Prophecy\ObjectProphecy
   *   The email.
   */
  protected function buildEmail(string $subtype, ?string $field_email_value): ObjectProphecy {
    $field_email = $this->prophesize(FieldItemListInterface::class);

    if ($field_email_value) {
      $field_email->isEmpty()->willReturn(FALSE);
      $field_email->getString()->willReturn($field_email_value);
    }
    else {
      $field_email->isEmpty()->willReturn(TRUE);
    }

    $contact_message = $this->prophesize(MessageInterface::class);
    $contact_message->hasField(Argument::exact('field_email'))->willReturn(TRUE);
    $contact_message->get(Argument::exact('field_email'))->willReturn($field_email->reveal());

    $email = $this->prophesize(EmailInterface::class);
    $email->getSubType()->willReturn($subtype);
    $email->getReplyTo()->willReturn([]);
    $email->setReplyTo(Argument::any())->will(static function (array $args) use ($email): void {
      $email->getReplyTo()->willReturn([$args[0]]);
    });
    $email->getParam(Argument::exact('contact_message'))->willReturn($contact_message->reveal());

    return $email;
  }

  /**
   * Tests that 'Reply-To' maile header is not changed for missing value.
   */
  public function testSetReplyToNoValue(): void {
    $email = $this->buildEmail('mail', NULL);

    $implementation = new ContactFormEmailBuild();
    $implementation($email->reveal());

    $calls = $email->findProphecyMethodCalls('setReplyTo', new ArgumentsWildcard([new AnyValuesToken()]),);
    self::assertCount(0, $calls);
  }

  /**
   * Tests that 'Reply-To' mail header is properly set.
   */
  public function testSetReplyTo(): void {
    $expected_reply_to = 'foo@example.com';
    $email = $this->buildEmail('mail', $expected_reply_to)->reveal();

    self::assertEquals([], $email->getReplyTo());

    $implementation = new ContactFormEmailBuild();
    $implementation($email);

    self::assertEquals([$expected_reply_to], $email->getReplyTo());
  }

}
