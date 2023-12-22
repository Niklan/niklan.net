<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Unit\Helper;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList;
use Drupal\niklan\Helper\EstimatedReadTimeCalculator;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for estimated read time calculator.
 *
 * @coversDefaultClass \Drupal\niklan\Helper\EstimatedReadTimeCalculator
 */
final class EstimatedReadTimeCalculatorTest extends UnitTestCase {

  /**
   * The fish text for testing.
   */
  protected string $fishText;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // This Lorem ipsum contains 401 word. Considering that words per minute is
    // set up for 143, this text should take ~2.8 minutes to read. The
    // calculator ceil result, that means this text is expected to return 3 as
    // a result.
    $this->fishText = <<<'TEXT'
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam risus tortor, consectetur non augue quis, pulvinar vehicula mi. Aenean mauris tortor, condimentum vitae placerat vel, malesuada nec tellus. Maecenas auctor ornare ligula sed cursus. Donec non purus eu metus iaculis condimentum. Pellentesque ut nisl at augue viverra luctus aliquet quis turpis. Aenean non nibh metus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
    Maecenas blandit, nisl ut imperdiet luctus, quam dui eleifend justo, ut sodales mauris velit in augue. Mauris ex ligula, porta eu mi nec, volutpat pharetra mi. Curabitur ac augue sit amet tortor viverra pulvinar eu vitae mauris. Nulla facilisi. Nullam imperdiet ante nec eros pulvinar cursus. Nunc sit amet commodo eros, ut lobortis enim. Ut nibh nulla, facilisis a tristique ut, malesuada non est. Etiam ultrices ante mi. Sed aliquam tristique gravida.
    Fusce eget lacus fermentum, aliquam urna quis, blandit dolor. Aliquam luctus libero at urna ullamcorper, vitae pharetra sapien euismod. Aliquam et venenatis nisi. Aenean volutpat nisl at bibendum maximus. Mauris molestie vitae enim et imperdiet. Aenean sed scelerisque enim. Phasellus vehicula eleifend ipsum, vitae tempor dolor sodales in. Nam ullamcorper pulvinar est eu pulvinar. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
    Sed posuere risus vel mauris finibus ullamcorper sit amet vel diam. Nam nisi odio, posuere nec convallis in, tristique eu mi. Interdum et malesuada fames ac ante ipsum primis in faucibus. Sed tincidunt dui id sapien lobortis viverra sed ut lacus. Duis et odio vitae lacus pretium dignissim at vitae metus. Vestibulum in leo imperdiet, sodales dui ut, sollicitudin ipsum. Curabitur turpis sapien, molestie vitae est blandit, maximus varius nisi.
    Nunc in convallis dui, eu fringilla nibh. Integer ac erat non augue ultricies faucibus. Aliquam posuere vitae dolor at sagittis. Vivamus convallis elit quis leo viverra laoreet. Sed sollicitudin, turpis at pellentesque ultricies, turpis libero condimentum augue, non congue est mi eu magna. Vestibulum molestie lorem eget sem commodo ullamcorper. Quisque laoreet pulvinar mauris. Quisque ac sapien lobortis, bibendum tortor nec, molestie ex. Praesent pharetra maximus rhoncus. Pellentesque finibus mi nibh, sed aliquam nisi posuere id. Ut iaculis maximus ipsum eget molestie. Quisque facilisis, enim quis dapibus posuere, velit velit euismod leo, at fringilla risus ipsum non ex. Aliquam tincidunt ligula eget tellus varius eleifend. Nulla elementum nisi elit, at finibus massa condimentum sodales. Etiam a metus in velit posuere accumsan vel at quam.
    TEXT;
  }

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
   * Tests calculation with empty items.
   */
  public function testCalculateEmpty(): void {
    $calculator = new EstimatedReadTimeCalculator();
    $result = $calculator->calculate($this->prepareFieldItemList());

    self::assertEquals(0, $result);
  }

  /**
   * Tests that for unknown type it fallbacks into 0.
   */
  public function testCalculateFallback(): void {
    $paragraph = $this->prophesize(ParagraphInterface::class);
    $paragraph->bundle()->willReturn('some_random_non_expected_bundle_name');

    $items = $this->prepareFieldItemList([$paragraph->reveal()]);

    $calculator = new EstimatedReadTimeCalculator();
    $result = $calculator->calculate($items);

    self::assertEquals(0, $result);
  }

  /**
   * Tests calculation for 'text' paragraph type with empty body.
   */
  public function testCalculateEmptyText(): void {
    $field_body = $this->prophesize(FieldItemListInterface::class);
    $field_body->isEmpty()->willReturn(TRUE);

    $paragraph = $this->prophesize(ParagraphInterface::class);
    $paragraph->bundle()->willReturn('text');
    $paragraph->get('field_body')->willReturn($field_body->reveal());

    $items = $this->prepareFieldItemList([$paragraph->reveal()]);

    $calculator = new EstimatedReadTimeCalculator();
    $result = $calculator->calculate($items);

    self::assertEquals(0, $result);
  }

  /**
   * Tests calculation for 'text' paragraph type.
   */
  public function testCalculateText(): void {
    $field_body_0 = $this->prophesize(TypedDataInterface::class);
    $field_body_0->getString()->willReturn($this->fishText);

    $field_body = $this->prophesize(FieldItemListInterface::class);
    $field_body->isEmpty()->willReturn(FALSE);
    $field_body->first()->willReturn($field_body_0->reveal());

    $paragraph = $this->prophesize(ParagraphInterface::class);
    $paragraph->bundle()->willReturn('text');
    $paragraph->get('field_body')->willReturn($field_body->reveal());

    $items = $this->prepareFieldItemList([$paragraph->reveal()]);

    $calculator = new EstimatedReadTimeCalculator();
    $result = $calculator->calculate($items);

    // Text using multiplier '2'.
    self::assertEquals(3 * 2, $result);
  }

  /**
   * Tests calculation for 'code' paragraph type with empty body.
   */
  public function testCalculateEmptyCode(): void {
    $field_body = $this->prophesize(FieldItemListInterface::class);
    $field_body->isEmpty()->willReturn(TRUE);

    $paragraph = $this->prophesize(ParagraphInterface::class);
    $paragraph->bundle()->willReturn('code');
    $paragraph->get('field_body')->willReturn($field_body->reveal());

    $items = $this->prepareFieldItemList([$paragraph->reveal()]);

    $calculator = new EstimatedReadTimeCalculator();
    $result = $calculator->calculate($items);

    self::assertEquals(0, $result);
  }

  /**
   * Tests calculation for 'code' paragraph type.
   */
  public function testCalculateCode(): void {
    $field_body_0 = $this->prophesize(TypedDataInterface::class);
    $field_body_0->getString()->willReturn($this->fishText);

    $field_body = $this->prophesize(FieldItemListInterface::class);
    $field_body->isEmpty()->willReturn(FALSE);
    $field_body->first()->willReturn($field_body_0->reveal());

    $paragraph = $this->prophesize(ParagraphInterface::class);
    $paragraph->bundle()->willReturn('code');
    $paragraph->get('field_body')->willReturn($field_body->reveal());

    $items = $this->prepareFieldItemList([$paragraph->reveal()]);

    $calculator = new EstimatedReadTimeCalculator();
    $result = $calculator->calculate($items);

    // Code using multiplier '3'.
    self::assertEquals(3 * 3, $result);
  }

}
