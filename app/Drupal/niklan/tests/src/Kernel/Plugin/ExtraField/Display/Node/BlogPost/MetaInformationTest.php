<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\ExtraField\Display\Node\BlogPost;

use Drupal\comment\Plugin\Field\FieldType\CommentItem;
use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\Core\Url;
use Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList;
use Drupal\node\NodeInterface;
use Drupal\Tests\niklan\Kernel\Plugin\ExtraField\ExtraFieldTestBase;
use Prophecy\Argument;

/**
 * Provides a test for 'meta_information' extra field.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\ExtraField\Display\Node\BlogPost\MetaInformation
 */
final class MetaInformationTest extends ExtraFieldTestBase {

  /**
   * Tests that extra field works as expected.
   */
  public function testView(): void {
    $field_content = $this
      ->prophesize(EntityReferenceRevisionsFieldItemList::class);

    $comment_count = $this->prophesize(TypedDataInterface::class);
    $comment_count->getValue()->willReturn(7);

    $comment_node_blog_entry_0 = $this->prophesize(CommentItem::class);
    $comment_node_blog_entry_0
      ->get('comment_count')
      ->willReturn($comment_count->reveal());

    $comment_node_blog_entry_list = $this
      ->prophesize(FieldItemListInterface::class);
    $comment_node_blog_entry_list
      ->first()
      ->willReturn($comment_node_blog_entry_0->reveal());

    $comments_url = Url::fromRoute('entity.node.canonical', ['node' => 1]);

    $node = $this->prophesize(NodeInterface::class);
    $node->getCreatedTime()->willReturn(123_456_789);
    $node
      ->get('comment_node_blog_entry')
      ->willReturn($comment_node_blog_entry_list->reveal());
    $node->toUrl(Argument::cetera())->willReturn($comments_url);
    $node->get('field_content')->willReturn($field_content->reveal());

    $plugin = $this->createExtraFieldDisplayInstance('meta_information');
    $plugin->setEntity($node->reveal());
    $build = $plugin->view($node->reveal());
    $this->render($build);

    self::assertCount(1, $this->cssSelect('.blog-meta'));
    self::assertRaw('0 min.');
    self::assertRaw('11/30/1973');
    self::assertRaw('7 comments');
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    DateFormat::create([
      'id' => 'fallback',
      'pattern' => 'D, m/d/Y - H:i',
    ])->save();
  }

}
