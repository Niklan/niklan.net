<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Plugin\Block\ExtraField\Display\Node;

use Drupal\node\Entity\Node;
use Drupal\Tests\niklan\Kernel\Plugin\ExtraField\ExtraFieldTestBase;
use Drupal\Tests\niklan\Traits\BlogEntryTrait;

/**
 * Provides a test for extra field with share functionality.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\ExtraField\Display\Node\Share
 */
final class ShareTest extends ExtraFieldTestBase {

  use BlogEntryTrait;

  /**
   * Tests that field works as expected.
   */
  public function testView(): void {
    $this->createBlogEntry(['title' => 'Node A'])->save();

    $plugin = $this->createExtraFieldDisplayInstance('share');
    $build = $plugin->view(Node::load(1));
    $this->render($build);

    self::assertCount(1, $this->cssSelect('.share'));
    self::assertRaw('node/1');
    self::assertRaw('telegram.me');
    self::assertRaw('twitter.com');
    self::assertRaw('vk.com');
    self::assertRaw('facebook.com');
    self::assertRaw('mailto:?subject');
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->setUpBlogEntry();
  }

}
