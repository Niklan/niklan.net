<?php

declare(strict_types=1);

namespace Drupal\niklan\Helper;

use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\niklan\Utility\Anchor;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Builds Table of Contents from paragraphs.
 */
final class TocBuilder {

  /**
   * Gets tree for paragraph field.
   *
   * @param \Drupal\Core\Field\EntityReferenceFieldItemListInterface $items
   *   The field contains paragraph references.
   *
   * @return array
   *   The array with tree of headings.
   */
  public function getTree(EntityReferenceFieldItemListInterface $items): array {
    $headings = $items->filter(
      static fn ($item) => $item->entity->bundle() == 'heading',
    );

    if ($headings->isEmpty()) {
      return [];
    }

    $links = [];
    foreach ($headings as $item) {
      /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
      $paragraph = $item->entity;
      $links[] = $this->prepareLink($paragraph, $links);
    }

    return $this->buildTree($links);
  }

  /**
   * Prepares link from heading paragraph.
   *
   * We need to assign every link some unique ID during the TOC generation
   * process. And then, this ID will be used for the next parsed link to set it
   * as parent or not.
   *
   * @param \Drupal\paragraphs\ParagraphInterface $paragraph
   *   The heading paragraph entity.
   * @param array $links
   *   The current set of links.
   *
   * @return array
   *   The link information.
   */
  protected function prepareLink(ParagraphInterface $paragraph, array $links = []): array {
    // Used for building tree, since we have flat tree of headings.
    $internal_id = &drupal_static(__FUNCTION__ . ':internal_id', 0);
    $internal_id++;

    $title = $paragraph->get('field_title')->getString();
    $heading_level = $paragraph->get('field_heading_level')->getString();

    switch ($heading_level) {
      case 'h2':
      default:
        $heading_level_int = 2;
        break;

      case 'h3':
        $heading_level_int = 3;
        break;

      case 'h4':
        $heading_level_int = 4;
        break;

      case 'h5':
        $heading_level_int = 5;
        break;

      case 'h6':
        $heading_level_int = 6;
        break;
    }

    foreach (\array_reverse($links) as $link) {
      if ($link['level'] < $heading_level_int) {
        $parent_id = $link['id'];
        break;
      }
    }

    if (!isset($parent_id)) {
      $parent_id = 0;
    }

    return [
      'id' => $internal_id,
      'text' => $title,
      'anchor' => Anchor::generate($title, 'toc', Anchor::REUSE),
      'level' => $heading_level_int,
      'parent_id' => $parent_id,
    ];
  }

  /**
   * Builds TOC tree.
   *
   * @param array $links
   *   The array with links.
   * @param int $parent_id
   *   The current parent id.
   *
   * @return array
   *   The array with links in tree format.
   */
  protected function buildTree(array $links, int $parent_id = 0): array {
    $tree = [];

    foreach ($links as $link) {
      if ($link['parent_id'] == $parent_id) {
        $children = $this->buildTree($links, $link['id']);

        if ($children) {
          $link['children'] = $children;
        }

        $tree[] = $link;
      }
    }

    return $tree;
  }

}
