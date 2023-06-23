<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Unit\Helper;

use Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList;
use Drupal\niklan\Helper\TocBuilder;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\Tests\niklan\Traits\ParagraphHeadingTrait;
use Drupal\Tests\UnitTestCase;

/**
 * Tests Table of Contents builder helper.
 *
 * @coversDefaultClass \Drupal\niklan\Helper\TocBuilder
 */
final class TocBuilderTest extends UnitTestCase {

  use ParagraphHeadingTrait;

  /**
   * Prepares prophecy for entity reference field.
   *
   * @param array $paragraphs
   *   An array with paragraph items.
   */
  protected function prepareFieldItemList(array $paragraphs = []): EntityReferenceRevisionsFieldItemList {
    $items = $this->prophesize(EntityReferenceRevisionsFieldItemList::class);
    $items->referencedEntities()->willReturn($paragraphs);

    return $items->reveal();
  }

  /**
   * Tests that builder properly processing empty field.
   */
  public function testTreeForEmptyField(): void {
    $items = $this->prepareFieldItemList();

    $builder = new TocBuilder();
    $tree = $builder->getTree($items);

    self::assertEquals([], $tree);
  }

  /**
   * Tests that builder properly processing non paragraph entities.
   */
  public function testTreeWithNonParagraphEntity(): void {
    $node = $this->prophesize(NodeInterface::class);

    $items = $this->prepareFieldItemList([$node->reveal()]);

    $builder = new TocBuilder();
    $tree = $builder->getTree($items);

    self::assertEquals([], $tree);
  }

  /**
   * Tests that builder properly processing not supported paragraph types.
   */
  public function testTreeWithUnsupportedParagraphTypes(): void {
    $paragraph_0 = $this->prophesize(ParagraphInterface::class);
    $paragraph_0->bundle()->willReturn('not_heading');

    $paragraph_1 = $this->prophesize(ParagraphInterface::class);
    $paragraph_1->bundle()->willReturn('definitely_not_heading');

    $paragraph_2 = $this->prophesize(ParagraphInterface::class);
    $paragraph_2->bundle()->willReturn('heading_not');

    $items = $this->prepareFieldItemList([
      $paragraph_0->reveal(),
      $paragraph_1->reveal(),
      $paragraph_2->reveal(),
    ]);

    $builder = new TocBuilder();
    $tree = $builder->getTree($items);

    self::assertEquals([], $tree);
  }

  /**
   * Tests for building a tree.
   */
  public function testTree(): void {
    $items = $this->prepareFieldItemList([
      $this->prepareHeadingParagraph('1', 'h2'),
      $this->prepareHeadingParagraph('2', 'h2'),
      $this->prepareHeadingParagraph('3', 'h3'),
      $this->prepareHeadingParagraph('3.1', 'h4'),
      $this->prepareHeadingParagraph('3.2', 'h4'),
      $this->prepareHeadingParagraph('3.2.1', 'h5'),
      $this->prepareHeadingParagraph('3.2.1.1', 'h6'),
      $this->prepareHeadingParagraph('3.2.1.2', 'h6'),
      $this->prepareHeadingParagraph('3.2.2', 'h5'),
      $this->prepareHeadingParagraph('3.3', 'h4'),
      $this->prepareHeadingParagraph('4', 'h2'),
    ]);

    $expected_tree = [
      [
        'id' => 1,
        'text' => '1',
        'anchor' => '1',
        'level' => 2,
        'parent_id' => 0,
        'children' => [],
      ],
      [
        'id' => 2,
        'text' => '2',
        'anchor' => '2',
        'level' => 2,
        'parent_id' => 0,
        'children' => [
          [
            'id' => 3,
            'text' => '3',
            'anchor' => '3',
            'level' => 3,
            'parent_id' => 2,
            'children' => [
              [
                'id' => 4,
                'text' => '3.1',
                'anchor' => '31',
                'level' => 4,
                'parent_id' => 3,
                'children' => [],
              ],
              [
                'id' => 5,
                'text' => '3.2',
                'anchor' => '32',
                'level' => 4,
                'parent_id' => 3,
                'children' => [
                  [
                    'id' => 6,
                    'text' => '3.2.1',
                    'anchor' => '321',
                    'level' => 5,
                    'parent_id' => 5,
                    'children' => [
                      [
                        'id' => 7,
                        'text' => '3.2.1.1',
                        'anchor' => '3211',
                        'level' => 6,
                        'parent_id' => 6,
                        'children' => [],
                      ],
                      [
                        'id' => 8,
                        'text' => '3.2.1.2',
                        'anchor' => '3212',
                        'level' => 6,
                        'parent_id' => 6,
                        'children' => [],
                      ],
                    ],
                  ],
                  [
                    'id' => 9,
                    'text' => '3.2.2',
                    'anchor' => '322',
                    'level' => 5,
                    'parent_id' => 5,
                    'children' => [],
                  ],
                ],
              ],
              [
                'id' => 10,
                'text' => '3.3',
                'anchor' => '33',
                'level' => 4,
                'parent_id' => 3,
                'children' => [],
              ],
            ],
          ],
        ],
      ],
      [
        'id' => 11,
        'text' => '4',
        'anchor' => '4',
        'level' => 2,
        'parent_id' => 0,
        'children' => [],
      ],
    ];

    $builder = new TocBuilder();
    $tree = $builder->getTree($items);

    self::assertEquals($expected_tree, $tree);
  }

}
