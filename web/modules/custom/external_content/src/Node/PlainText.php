<?php declare(strict_types = 1);

namespace Drupal\external_content\Node;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\Data;

/**
 * Represents a simple plain text inside elements.
 */
final class PlainText extends Node {

  /**
   * Constructs a new PlainText object.
   *
   * @param string $text
   *   The text content.
   */
  public function __construct(
    protected string $text,
  ) {}

  /**
   * Gets content.
   */
  public function getContent(): string {
    return $this->text;
  }

  /**
   * {@inheritdoc}
   */
  public function serialize(): Data {
    return new Data([
      'text' => $this->text,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public static function deserialize(Data $data): NodeInterface {
    return new self($data->get('text'));
  }

}
