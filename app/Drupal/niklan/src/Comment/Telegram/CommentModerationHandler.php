<?php

declare(strict_types=1);

namespace Drupal\niklan\Comment\Telegram;

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

    $new_comment = new TranslatableMarkup('New comment');
    $publication_property = new TranslatableMarkup('Publication');
    $publication_label = $comment->getCommentedEntity()->label();
    $publication_url = $comment->getCommentedEntity()->toUrl()->setAbsolute()->toString();
    $author_property = new TranslatableMarkup('Author');
    $author_name = \implode('; ', [
      $comment->getAuthorName(),
      $comment->getAuthorEmail(),
      $comment->getHomepage(),
    ]);
    $comment_body = $comment->get('comment_body')->value;

    $text = <<<HTML
    <strong>$new_comment</strong>
    
    • <strong>$publication_property</strong>: <a href="$publication_url">$publication_label</a>
    • <strong>$author_property</strong>: $author_name
    
    <blockquote expandable>$comment_body</blockquote>
    HTML;

    $this->telegram->getBot()->sendMessage(
      text: $text,
      parse_mode: ParseMode::HTML,
      link_preview_options: LinkPreviewOptions::make(TRUE),
      chat_id: Settings::get('telegram_chat_id'),
      reply_markup: $this->defaultReplyMarkup((string) $comment->id()),
    );
  }

  public function onCallbackQuery(Nutgram $bot): void {
    \preg_match_all('/([a-z-]+:[a-z-]+):([0-9]+)/', $bot->callbackQuery()->data, $matches, \PREG_SET_ORDER);
    [, $callback_type, $comment_id] = $matches[0];

    if (!CommentModerationCallbackType::tryFrom($callback_type) || !\is_numeric($comment_id)) {
      return;
    }

    $comment = $this->loadComment($comment_id);

    if (!$comment) {
      $bot->answerCallbackQuery(text: (string) new TranslatableMarkup('Comment not found'));
      // If comment is missing, remove buttons, there is no reason to keep them.
      $this->removeButtons($bot);

      return;
    }

    match ($callback_type) {
      CommentModerationCallbackType::Approve->value => $bot->onApprove($comment, $bot),
    };
  }

  private function defaultReplyMarkup(string $comment_id): InlineKeyboardMarkup {
    return InlineKeyboardMarkup::make()
      ->addRow(
        new InlineKeyboardButton(
          text: (string) new TranslatableMarkup('Approve', [], ['context' => 'comment moderation']),
          callback_data: CommentModerationCallbackType::Approve->buildCallbackId($comment_id),
        ),
        new InlineKeyboardButton(
          text: (string) new TranslatableMarkup('Delete'),
          callback_data: CommentModerationCallbackType::Delete->buildCallbackId($comment_id),
        ),
      );
  }

  private function loadComment(string $comment_id): ?Comment {
    return $this->entityTypeManager->getStorage('comment')->load($comment_id);
  }

  private function onApprove(Comment $comment, Nutgram $bot): void {
    $comment->setPublished();
    $comment->save();

    $bot->answerCallbackQuery(text: (string) new TranslatableMarkup('Comment has been approved and published'));
    $this->removeButtons($bot);
  }

  private function removeButtons(Nutgram $bot): void {
    $bot->editMessageReplyMarkup();
  }

}
