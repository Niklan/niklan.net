<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\paragraphs\Behavior;

use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\paragraphs\ParagraphsBehaviorInterface;
use Drupal\paragraphs\ParagraphsBehaviorManager;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;

/**
 * Provides a test base for paragraph behaviors.
 */
abstract class BehaviorTest extends NiklanTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'paragraphs',
  ];

  /**
   * The behavior plugin manager.
   */
  protected ParagraphsBehaviorManager $behaviorPluginManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('paragraph');

    $this->behaviorPluginManager = $this
      ->container
      ->get('plugin.manager.paragraphs.behavior');
  }

  /**
   * Gets paragraph behavior form.
   *
   * @param string $plugin_id
   *   The plugin ID.
   * @param \Drupal\paragraphs\ParagraphInterface $paragraph
   *   The paragraph entity.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The plugin form.
   */
  protected function getBehaviorForm(string $plugin_id, ParagraphInterface $paragraph, FormStateInterface $form_state): array {
    $plugin = $this->behaviorPluginManager->createInstance($plugin_id);
    \assert($plugin instanceof ParagraphsBehaviorInterface);

    $main_form = [];

    return $plugin->buildBehaviorForm($paragraph, $main_form, $form_state);
  }

}
