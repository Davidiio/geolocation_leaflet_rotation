<?php

namespace Drupal\geolocation_leaflet_rotation\Plugin\geolocation\MapFeature;

use Drupal\Component\Serialization\Json;
use Drupal\geolocation\MapFeatureBase;
use Drupal\Core\Render\BubbleableMetadata;

/**
 * Provides marker clusterer.
 *
 * @MapFeature(
 *   id = "leaflet_rotate",
 *   name = @Translation("Leaflet Rotate"),
 *   description = @Translation("Allow map rotation."),
 *   type = "leaflet",
 * )
 */
class LeafletRotate extends MapFeatureBase {

  /**
   * {@inheritdoc}
   */
  public static function getDefaultSettings() {
    $default_settings = parent::getDefaultSettings();

    $default_settings['bearing'] = 0;
    $default_settings['display_control'] = true;

    return $default_settings;
  }

  /**
   * {@inheritdoc}
   */
  public function getSettingsForm(array $settings, array $parents) {

    $form['display_control'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display control'),
      '#default_value' => $settings['display_control'],
    ];

    $form['bearing'] = [
      '#type' => 'number',
      '#min' => -360,
      '#max' => 360,
      '#step' => .01,
      '#title' => $this->t('Bearing'),
      '#description' => $this->t('Map initial rotation in degrees.'),
      '#default_value' => $settings['bearing'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function alterMap(array $render_array, array $feature_settings, array $context = []) {
    $render_array = parent::alterMap($render_array, $feature_settings, $context);

    $render_array['#attached'] = BubbleableMetadata::mergeAttachments(
      empty($render_array['#attached']) ? [] : $render_array['#attached'],
      [
        'library' => [
          'geolocation_leaflet_rotation/leaflet.rotate',
          'geolocation_leaflet_rotation/mapfeature.' . $this->getPluginId(),
        ],
        'drupalSettings' => [
          'geolocation' => [
            'maps' => [
              $render_array['#id'] => [
                $this->getPluginId() => [
                  'bearing' => $feature_settings['bearing'],
                  'display_control' => $feature_settings['display_control'],
                ],
              ],
            ],
          ],
        ],
      ]
    );

    return $render_array;
  }

}
