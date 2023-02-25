<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\paragraphs\Behavior;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Form\FormState;
use Drupal\niklan\Plugin\paragraphs\Behavior\CodeLineHighlight;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\paragraphs\ParagraphsTypeInterface;

/**
 * Provides a test for code line highlight behavior.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\paragraphs\Behavior\CodeLineHighlight
 */
final class CodeLineHighlightTest extends BehaviorTest {

  /**
   * The code paragraph type.
   */
  protected ParagraphsTypeInterface $codeParagraphType;

  /**
   * Tests that is applicable allows only 'code' paragraph.
   */
  public function testIsApplicable(): void {
    $not_a_code = ParagraphsType::create([
      'id' => 'not_a_code',
    ]);

    self::assertFalse(CodeLineHighlight::isApplicable($not_a_code));
    self::assertTrue(CodeLineHighlight::isApplicable($this->codeParagraphType));
  }

  /**
   * Tests view alteration.
   */
  public function testView(): void {
    $code_highlight = $this
      ->behaviorPluginManager
      ->createInstance('niklan_paragraphs_code_line_highlight');
    \assert($code_highlight instanceof CodeLineHighlight);

    $paragraph = Paragraph::create([
      'type' => 'code',
    ]);
    $paragraph->save();

    $display = $this->prophesize(EntityViewDisplayInterface::class);

    $builds = [];
    $code_highlight->view($builds, $paragraph, $display->reveal(), 'default');

    $expected_build = [
      '#attached' => [
        'library' => [
          'niklan/code_line_highlight',
        ],
      ],
      '#attributes' => [
        'data-highlighted-lines' => '',
      ],
    ];
    self::assertEquals($expected_build, $builds);

    $paragraph->setBehaviorSettings(
      'niklan_paragraphs_code_line_highlight',
      ['highlighted_lines' => '10;20'],
    );
    $paragraph->save();

    $builds = [];
    $code_highlight->view($builds, $paragraph, $display->reveal(), 'default');

    $expected_build = [
      '#attached' => [
        'library' => [
          'niklan/code_line_highlight',
        ],
      ],
      '#attributes' => [
        'data-highlighted-lines' => '10;20',
      ],
    ];
    self::assertEquals($expected_build, $builds);
  }

  /**
   * Tests behavior form works properly.
   */
  public function testBehaviorForm(): void {
    $form_state = new FormState();

    $paragraph = Paragraph::create([
      'type' => 'code',
    ]);
    $paragraph->save();

    $plugin_form = $this->getBehaviorForm(
      'niklan_paragraphs_code_line_highlight',
      $paragraph,
      $form_state,
    );
    self::assertEquals('', $plugin_form['highlighted_lines']['#default_value']);

    $paragraph->setBehaviorSettings(
      'niklan_paragraphs_code_line_highlight',
      ['highlighted_lines' => '10;20'],
    );
    $paragraph->save();

    $plugin_form = $this->getBehaviorForm(
      'niklan_paragraphs_code_line_highlight',
      $paragraph,
      $form_state,
    );
    self::assertEquals('10;20', $plugin_form['highlighted_lines']['#default_value']);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $code = ParagraphsType::create([
      'id' => 'code',
    ]);
    $code->save();
    $this->codeParagraphType = $code;
  }

}
