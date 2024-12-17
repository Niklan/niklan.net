<?php

declare(strict_types=1);

namespace Drupal\niklan\Comment\Telegram;

use Drupal\comment\CommentInterface;
use Drupal\comment\CommentStorageInterface;
use Drupal\comment\Entity\Comment;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Telegram\Telegram;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Message\LinkPreviewOptions;
use SergiX44\Nutgram\Telegram\Types\Reaction\ReactionTypeEmoji;

final readonly class CommentModerationHandler {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    private Telegram $telegram,
  ) {}

  public function handle(Comment $comment): void {
    // Published comments doesn't need moderation.
    if ($comment->isPublished()) {
      return;
    }

    $this->telegram->getBot()->sendMessage(
      text: $this->buildMessageText($comment),
      chat_id: Settings::get('telegram_chat_id'),
      parse_mode: ParseMode::HTML,
      link_preview_options: LinkPreviewOptions::make(TRUE),
      reply_markup: $this->defaultReplyMarkup($comment),
    );
  }

  public function onCallbackQuery(Nutgram $bot): void {
    \preg_match_all('/([a-z-]+:[a-z-]+):([0-9]+)/', $bot->callbackQuery()->data, $matches, \PREG_SET_ORDER);
    [, $callback_type, $comment_id] = $matches[0];

    if (!CommentModerationCallbackType::tryFrom($callback_type) || !\is_numeric($comment_id)) {
      return;
    }

    $comment = $this->storage()->load($comment_id);

    if (!$comment instanceof CommentInterface || $comment->isPublished()) {
      // If comment is missing or already published, remove buttons, there is no
      // reason to keep them.
      $this->removeButtons($bot);

      return;
    }

    match ($callback_type) {
      CommentModerationCallbackType::Approve->value => $this->onApprove($comment, $bot),
      CommentModerationCallbackType::Delete->value => $this->onDelete($comment, $bot),
      CommentModerationCallbackType::DeleteConfirm->value => $this->onDeleteConfirm($comment, $bot),
      CommentModerationCallbackType::DeleteCancel->value => $this->onDeleteCancel($comment, $bot),
      default => NULL,
    };
  }

  private function buildMessageText(Comment $comment): string {
    $new_comment = new TranslatableMarkup('New comment');
    $publication_property = new TranslatableMarkup('Publication');
    $publication_label = $comment->getCommentedEntity()->label();
    $publication_url = $comment->getCommentedEntity()->toUrl()->setAbsolute()->toString();
    $author_property = new TranslatableMarkup('Author');
    $author_name = $comment->getAuthorName();
    $author_email_property = new TranslatableMarkup('Email');
    $author_email = $comment->getAuthorEmail();
    $author_homepage_property = new TranslatableMarkup('Homepage');
    $author_homepage = $comment->getHomepage();

    $comment_body = \htmlentities($comment->get('comment_body')->first()->get('value')->getValue());

    return <<<HTML
    <strong>💬 $new_comment</strong>
    
    • <strong>$publication_property</strong>: <a href="$publication_url">$publication_label</a>
    • <strong>$author_property</strong>: $author_name
    • <strong>$author_email_property</strong>: $author_email
    • <strong>$author_homepage_property</strong>: $author_homepage
    
    <blockquote expandable>$comment_body</blockquote>
    HTML;
  }

  private function defaultReplyMarkup(Comment $comment): InlineKeyboardMarkup {
    return InlineKeyboardMarkup::make()
      ->addRow(
        new InlineKeyboardButton(
          text: (string) new TranslatableMarkup('Approve', [], ['context' => 'comment moderation']),
          callback_data: CommentModerationCallbackType::Approve->buildCallbackId($comment->id()),
        ),
        new InlineKeyboardButton(
          text: (string) new TranslatableMarkup('Delete'),
          callback_data: CommentModerationCallbackType::Delete->buildCallbackId($comment->id()),
        ),
      )
      ->addRow(
        new InlineKeyboardButton(
          text: (string) new TranslatableMarkup('Edit on website'),
          url: $comment->toUrl('edit-form')->setAbsolute()->toString(),
        ),
      );
  }

  private function deleteConfirmReplyMarkup(string $comment_id): InlineKeyboardMarkup {
    return InlineKeyboardMarkup::make()
      ->addRow(
        new InlineKeyboardButton(
          text: (string) new TranslatableMarkup('Confirm deletion'),
          callback_data: CommentModerationCallbackType::DeleteConfirm->buildCallbackId($comment_id),
        ),
        new InlineKeyboardButton(
          text: (string) new TranslatableMarkup('Cancel'),
          callback_data: CommentModerationCallbackType::DeleteCancel->buildCallbackId($comment_id),
        ),
      );
  }

  private function onDelete(Comment $comment, Nutgram $bot): void {
    $bot->editMessageReplyMarkup(reply_markup: $this->deleteConfirmReplyMarkup($comment->id()));
  }

  private function onDeleteCancel(Comment $comment, Nutgram $bot): void {
    $bot->editMessageReplyMarkup(reply_markup: $this->defaultReplyMarkup($comment));
  }

  private function onDeleteConfirm(Comment $comment, Nutgram $bot): void {
    $this->storage()->delete([$comment]);
    $bot->answerCallbackQuery(text: (string) new TranslatableMarkup('Comment has been deleted'));
    $this->removeButtons($bot);
    $bot->setMessageReaction([ReactionTypeEmoji::make(ReactionTypeEmoji::THUMBS_DOWN)]);
  }

  private function storage(): CommentStorageInterface {
    return $this->entityTypeManager->getStorage('comment');
  }

  private function onApprove(Comment $comment, Nutgram $bot): void {
    $comment->setPublished();
    $this->storage()->save($comment);

    $bot->answerCallbackQuery(text: (string) new TranslatableMarkup('Comment has been approved and published'));
    $bot->setMessageReaction([ReactionTypeEmoji::make(ReactionTypeEmoji::THUMBS_UP)]);
    $this->removeButtons($bot);
  }

  private function removeButtons(Nutgram $bot): void {
    $bot->editMessageReplyMarkup();
  }

}
