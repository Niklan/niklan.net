<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\paragraphs\Behavior;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\paragraphs\ParagraphsBehaviorBase;

/**
 * Provides a code highlight behavior.
 *
 * @ParagraphsBehavior(
 *   id = "niklan_paragraphs_code_line_highlight",
 *   label = "Code line highlight (new)",
 *   description = "Highlight code line to improve focus on.",
 *   weight = 0,
 * )
 */
final class CodeLineHighlight extends ParagraphsBehaviorBase {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(ParagraphsType $paragraphs_type): bool {
    return $paragraphs_type->id() === 'code';
  }

  /**
   * {@inheritdoc}
   */
  public function view(array &$build, Paragraph $paragraph, EntityViewDisplayInterface $display, $view_mode): void {
    $build['#attached']['library'][] = 'niklan/code_line_highlight';
    $build['#attributes']['data-highlighted-lines'] = $paragraph->getBehaviorSetting(
      $this->getPluginId(),
      'highlighted_lines',
      '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildBehaviorForm(ParagraphInterface $paragraph, array &$form, FormStateInterface $form_state): array {
    $form['highlighted_lines'] = [
      '#type' => 'textfield',
      '#title' => $this->t("Highlighted lines"),
      '#description' => $this->t(
        'Separate line numbers with commas, and range with :.',
      ),
      '#default_value' => $paragraph->getBehaviorSetting(
        $this->getPluginId(),
        'highlighted_lines',
        '',
      ),
    ];

    return $form;
  }

}
