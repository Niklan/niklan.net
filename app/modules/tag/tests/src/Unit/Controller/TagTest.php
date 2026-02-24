<?php

declare(strict_types=1);

namespace Drupal\Tests\app_tag\Unit\Controller;

use Drupal\app_contract\Contract\Node\Node;
use Drupal\app_tag\Controller\Tag;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Prophecy\Argument;

#[CoversClass(Tag::class)]
final class TagTest extends UnitTestCase {

  public function testThemeName(): void {
    $controller = $this->buildController();
    $term = $this->buildTerm();

    $result = $controller($term);

    self::assertSame('app_blog_list', $result['#theme']);
  }

  public function testTitle(): void {
    $term = $this->buildTerm('PHP');

    $result = Tag::title($term);

    self::assertSame('Publications with the @name tag', $result->getUntranslatedString());
  }

  public function testNoTitleInBuildArray(): void {
    $controller = $this->buildController();
    $term = $this->buildTerm();

    $result = $controller($term);

    self::assertArrayNotHasKey('#title', $result);
  }

  public function testPager(): void {
    $controller = $this->buildController();
    $term = $this->buildTerm();

    $result = $controller($term);

    self::assertSame('pager', $result['#pager']['#type']);
  }

  public function testCacheTags(): void {
    $controller = $this->buildController();
    $term = $this->buildTerm();

    $result = $controller($term);

    self::assertContains('node_list', $result['#cache']['tags']);
  }

  public function testEmptyItems(): void {
    $controller = $this->buildController();
    $term = $this->buildTerm();

    $result = $controller($term);

    self::assertSame([], $result['#items']);
  }

  public function testWithItems(): void {
    $node = $this->prophesize(Node::class);

    $query = $this->prophesize(QueryInterface::class);
    $query->accessCheck(FALSE)->willReturn($query->reveal());
    $query->condition(Argument::cetera())->willReturn($query->reveal());
    $query->sort(Argument::cetera())->willReturn($query->reveal());
    $query->pager()->willReturn($query->reveal());
    $query->execute()->willReturn([1, 2]);

    $storage = $this->prophesize(EntityStorageInterface::class);
    $storage->getQuery()->willReturn($query->reveal());
    $storage->loadMultiple([1, 2])->willReturn([
      $node->reveal(),
      $node->reveal(),
    ]);

    $view_builder = $this->prophesize(EntityViewBuilderInterface::class);
    $view_builder->view(Argument::any(), 'teaser')->willReturn(['#markup' => 'rendered']);

    $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
    $entity_type_manager->getStorage('node')->willReturn($storage->reveal());
    $entity_type_manager->getViewBuilder('node')->willReturn($view_builder->reveal());

    $controller = new Tag($entity_type_manager->reveal());
    $term = $this->buildTerm();

    $result = $controller($term);

    self::assertCount(2, $result['#items']);
  }

  private function buildController(): Tag {
    $query = $this->prophesize(QueryInterface::class);
    $query->accessCheck(Argument::any())->willReturn($query->reveal());
    $query->condition(Argument::cetera())->willReturn($query->reveal());
    $query->sort(Argument::cetera())->willReturn($query->reveal());
    $query->pager()->willReturn($query->reveal());
    $query->execute()->willReturn([]);

    $storage = $this->prophesize(EntityStorageInterface::class);
    $storage->getQuery()->willReturn($query->reveal());
    $storage->loadMultiple([])->willReturn([]);

    $view_builder = $this->prophesize(EntityViewBuilderInterface::class);

    $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
    $entity_type_manager->getStorage('node')->willReturn($storage->reveal());
    $entity_type_manager->getViewBuilder('node')->willReturn($view_builder->reveal());

    return new Tag($entity_type_manager->reveal());
  }

  private function buildTerm(string $label = 'Test'): TermInterface {
    $term = $this->prophesize(TermInterface::class);
    $term->id()->willReturn(1);
    $term->label()->willReturn($label);

    return $term->reveal();
  }

}
