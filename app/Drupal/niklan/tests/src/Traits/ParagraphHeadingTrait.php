<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Traits;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Provides trait for 'heading' paragraph.
 */
trait ParagraphHeadingTrait {

  /**
   * Builds prophecy with heading paragraph.
   *
   * @param string $title
   *   The paragraph title.
   * @param string $heading_level
   *   The paragraph heading level.
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   The paragraph revealed prophecy.
   */
  protected function prepareHeadingParagraph(string $title, string $heading_level): ParagraphInterface {
    $paragraph = $this->prophesize(ParagraphInterface::class);
    $paragraph->bundle()->willReturn('heading');

    $field_title_items = $this->prophesize(FieldItemListInterface::class);
    $field_title_items->getString()->willReturn($title);
    $paragraph->get('field_title')->willReturn($field_title_items->reveal());

    $field_heading_level_items = $this
      ->prophesize(FieldItemListInterface::class);
    $field_heading_level_items->getString()->willReturn($heading_level);
    $paragraph
      ->get('field_heading_level')
      ->willReturn($field_heading_level_items->reveal());

    return $paragraph->reveal();
  }

}
