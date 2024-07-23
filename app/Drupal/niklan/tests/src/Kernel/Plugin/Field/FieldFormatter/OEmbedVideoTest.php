<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Field\FieldFormatter;

use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormState;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\Entity\Media;
use Drupal\media\OEmbed\Provider;
use Drupal\media\OEmbed\Resource;
use Drupal\media\OEmbed\ResourceFetcherInterface;
use Drupal\media\OEmbed\UrlResolverInterface;
use Drupal\niklan\Plugin\Field\FieldFormatter\OEmbedVideo;
use Drupal\responsive_image\Entity\ResponsiveImageStyle;
use Drupal\Tests\media\Traits\MediaTypeCreationTrait;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Prophecy\Argument;

/**
 * Provides a test for 'niklan_media_remote_video_optimized' formatter.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\Field\FieldFormatter\OEmbedVideo
 */
final class OEmbedVideoTest extends NiklanTestBase {

  use MediaTypeCreationTrait;

  /**
   * Tests that formatter only applicable for media entity type.
   */
  public function testIsApplicableOnNonMediaEntity(): void {
    $field_definition = $this->prophesize(FieldDefinitionInterface::class);
    $field_definition->getTargetEntityTypeId()->willReturn('node');

    self::assertFalse(OEmbedVideo::isApplicable($field_definition->reveal()));
  }

  /**
   * Tests that formatter only applicable for OEmbed video sources.
   */
  public function testIsApplicableOnNonOembedVideoSource(): void {
    $this->createMediaType('image', ['id' => 'image']);

    $field_definition = $this->prophesize(FieldDefinitionInterface::class);
    $field_definition->getTargetEntityTypeId()->willReturn('media');
    $field_definition->getTargetBundle()->willReturn('image');

    self::assertFalse(OEmbedVideo::isApplicable($field_definition->reveal()));
  }

  /**
   * Tests that formatter silently exit if media type is missing.
   */
  public function testIsApplicableOnMissingMediaType(): void {
    $field_definition = $this->prophesize(FieldDefinitionInterface::class);
    $field_definition->getTargetEntityTypeId()->willReturn('media');
    $field_definition->getTargetBundle()->willReturn('image');

    self::assertFalse(OEmbedVideo::isApplicable($field_definition->reveal()));
  }

  /**
   * Tests that formatter works with OEmbed video source media type.
   */
  public function testIsApplicableOnValidMediaType(): void {
    $field_definition = $this->prophesize(FieldDefinitionInterface::class);
    $field_definition->getTargetEntityTypeId()->willReturn('media');
    $field_definition->getTargetBundle()->willReturn('video');

    self::assertTrue(OEmbedVideo::isApplicable($field_definition->reveal()));
  }

  /**
   * Tests that settings form allows to set up all expected settings.
   */
  public function testSettingsForm(): void {
    $formatter = $this->getFormatterInstance();
    $plugin_form = $formatter->settingsForm([], new FormState());

    self::assertArrayHasKey('responsive_image_style', $plugin_form);
  }

  /**
   * Tests that settings form skips responsive image styles without mapping.
   */
  public function testSettingsFormSkipImageStylesWithoutMapping(): void {
    $style = ResponsiveImageStyle::create([
      'id' => 'without_mapping',
      'label' => 'Without mapping',
    ]);
    $style->save();

    $formatter = $this->getFormatterInstance();
    $plugin_form = $formatter->settingsForm([], new FormState());

    self::assertArrayHasKey('responsive_image_style', $plugin_form);
    $image_style_options = $plugin_form['responsive_image_style']['#options'];
    self::assertArrayHasKey('fallback', $image_style_options);
    self::assertArrayNotHasKey('without_mapping', $image_style_options);
  }

  /**
   * Tests that settings summary contains all expected summary.
   *
   * @param string|null $setting_name
   *   The setting name.
   * @param string|null $setting_value
   *   The settings value.
   * @param string $expected_summary
   *   The expected summary.
   *
   * @dataProvider settingsSummaryDataProvider
   */
  public function testSettingsSummary(?string $setting_name, ?string $setting_value, string $expected_summary): void {
    $formatter = $this->getFormatterInstance();

    if ($setting_value) {
      $formatter->setSetting($setting_name, $setting_value);
    }

    $summary = $formatter->settingsSummary();

    // They are most likely is translatable strings.
    foreach ($summary as $delta => $value) {
      $summary[$delta] = (string) $value;
    }

    self::assertContains($expected_summary, $summary);
  }

  /**
   * Provides settings summary data for testing.
   */
  public function settingsSummaryDataProvider(): \Generator {
    yield [NULL, NULL, 'Select a responsive image style.'];
    yield [
      'responsive_image_style',
      'fallback',
      'Responsive image style: Fallback',
    ];
  }

  /**
   * Tests formatter building elements.
   */
  public function testFormatter(): void {
    $this->setFormatterSettings([
      'responsive_image_style' => 'fallback',
    ]);

    ImageStyle::create(['name' => 'thumbnail'])->save();

    $this->container->set(
      'media.oembed.url_resolver',
      $this->prophesize(UrlResolverInterface::class)->reveal(),
    );

    $provider = $this->prophesize(Provider::class);
    $provider->getName()->willReturn('YouTube');
    $resource = Resource::video('<html></html>', 16, 16, $provider->reveal());

    $resource_fetcher = $this->prophesize(ResourceFetcherInterface::class);
    $resource_fetcher->fetchResource(Argument::any())->willReturn($resource);
    $this->container->set(
      'media.oembed.resource_fetcher',
      $resource_fetcher->reveal(),
    );

    $media = Media::create([
      'bundle' => 'video',
      'name' => 'Test video',
      'field_media_oembed_video' => 'https://youtu.be/oBpxD7Rcmjg',
    ]);
    $media->save();

    $view_builder = $this
      ->container
      ->get('entity_type.manager')
      ->getViewBuilder('media');
    $build = $view_builder->view($media, 'default');
    $this->render($build);

    self::assertCount(1, $this->cssSelect('.oembed-video'));
    self::assertRaw('oBpxD7Rcmjg');
  }

  /**
   * Gets formatter instance.
   */
  protected function getFormatterInstance(): OEmbedVideo {
    $field_definitions = $this
      ->container
      ->get('entity_field.manager')
      ->getFieldDefinitions('media', 'video');

    return $this
      ->container
      ->get('plugin.manager.field.formatter')
      ->getInstance([
        'configuration' => [
          'type' => 'niklan_media_remote_video_optimized',
        ],
        'field_definition' => $field_definitions['field_media_oembed_video'],
      ]);
  }

  /**
   * Updates formatter settings for tested field.
   *
   * @param array $settings
   *   The formatter settings.
   */
  protected function setFormatterSettings(array $settings): void {
    $this
      ->container
      ->get('entity_display.repository')
      ->getViewDisplay('media', 'video', 'default')
      ->setComponent('field_media_oembed_video', [
        'type' => 'niklan_media_remote_video_optimized',
        'settings' => $settings,
      ])
      ->save();
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installSchema('file', ['file_usage']);
    $this->installEntitySchema('file');
    $this->installEntitySchema('media');
    $this->createMediaType('oembed:video', ['id' => 'video']);

    $style = ResponsiveImageStyle::create([
      'id' => 'fallback',
      'label' => 'Fallback',
    ]);
    $style->addImageStyleMapping('test_breakpoint', '1x', [
      'image_mapping_type' => 'image_style',
      'image_mapping' => 'small',
    ]);
    $style->save();

    DateFormat::create([
      'id' => 'fallback',
      'pattern' => 'D, m/d/Y - H:i',
    ])->save();
  }

}
