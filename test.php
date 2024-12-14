<?php

/**
 * @file
 * \Drupal\niklan\Telegram\Controller\WebhookController::setWebhook();
 */

use Drupal\Core\Site\Settings;
use Drupal\niklan\Telegram\Telegram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

$telegram = Drupal::service(Telegram::class);
assert($telegram instanceof Telegram);
$telegram->getBot()->sendMessage(
  chat_id: Settings::get('telegram_chat_id'),
  text: 'Test',
  reply_markup: InlineKeyboardMarkup::make()
    ->addRow(
      new InlineKeyboardButton(text: '✅ Approve', callback_data: 'approve@commentModerator'),
      new InlineKeyboardButton(text: '❌ Remove', callback_data: 'remove@commentModerator'),
  ),
);
