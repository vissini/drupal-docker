<?php

namespace Drupal\faqfield\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'faqfield_accordion' formatter.
 *
 * @FieldFormatter(
 *   id = "faqfield_accordion",
 *   label = @Translation("jQuery Accordion"),
 *   field_types = {
 *     "faqfield"
 *   }
 * )
 */
class FaqFieldAccordionFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'active' => 0,
      'heightStyle' => 'auto',
      'collapsible' => FALSE,
      'event' => 'click',
      'animate' => [
        'easing' => 'linear',
        'duration' => 200,
      ],
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);
    // Number of first active element.
    $elements['active'] = [
      '#type' => 'number',
      '#title' => $this->t('Default active'),
      '#placeholder' => $this->t('None'),
      '#default_value' => $this->getSetting('active'),
      '#description' => $this->t('Index of the active question starting from 0. If left empty and <em>Fully collapsible</em> is on, no question will be opened by default.'),
      '#maxlength' => 3,
      '#size' => 5,
    ];
    // Whether auto heigth is enabled.
    $elements['heightStyle'] = [
      '#type' => 'select',
      '#title' => $this->t('Height style'),
      '#default_value' => $this->getSetting('heightStyle'),
      '#options' => [
        'auto' => $this->t('Auto : All panels will be set to the height of the tallest question.'),
        'fill' => $this->t('Fill : Expand to the available height based on the accordions question height.'),
        'content' => $this->t('Content : Each panel will be only as tall as its question.'),
      ],
      '#description' => $this->t('Controls the height of the accordion and each panel.'),
    ];
    // Whether elements are collabsible.
    $elements['collapsible'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Fully collapsible'),
      '#default_value' => $this->getSetting('collapsible'),
      '#description' => $this->t('Whether all the questions can be closed at once. Allows collapsing the active section.'),
    ];
    // Name of triggering event.
    $elements['event'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event'),
      '#placeholder' => 'click',
      '#default_value' => $this->getSetting('event'),
      '#description' => $this->t('The event on which to open a question. Multiple events can be specified, separated by a space.'),
    ];
    // Animation options for the accordion formatter.
    $elements['animate'] = [
      '#type' => 'details',
      '#title' => $this->t('Animation settings'),
      '#collapsed' => TRUE,
    ];
    // Animation duration in milliseconds with the selected easing.
    $elements['animate']['duration'] = [
      '#type' => 'number',
      '#title' => $this->t('Duration'),
      '#default_value' => $this->getSetting('animate')['duration'],
      '#description' => $this->t('Animation duration in milliseconds with the selected easing.'),
      '#min' => 0,
    ];
    // Name of easing to use when the event is triggered.
    $elements['animate']['easing'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Easing'),
      '#placeholder' => 'linear',
      '#default_value' => $this->getSetting('animate')['easing'],
      '#description' => $this->t('Name of <a href="@link">easing</a> to use when the event is triggered.', ['@link' => 'http://api.jqueryui.com/easings/']),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    if (is_numeric($this->getSetting('active'))) {
      $active = $this->getSetting('active');
    }
    else {
      $active = $this->t('None');
    }
    $summary[] = $this->t('Default active: @element', ['@element' => $active]);
    $height_style = '';
    switch ($this->getSetting('heightStyle')) {
      case 'auto':
        $height_style = $this->t('Auto');
        break;

      case 'fill':
        $height_style = $this->t('Fill');
        break;

      case 'content':
        $height_style = $this->t('Content');
        break;
    }
    $summary[] = $this->t('Height style : @style', ['@style' => $height_style]);
    if ($this->getSetting('collapsible')) {
      $summary[] = $this->t('Fully collapsible');
    }
    $summary[] = $this->t('Event: @event', ['@event' => $this->getSetting('event')]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   *
   * This will not be themeable, because changes would break jQuery UI
   * accordion functionality!
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $default_format = $this->getFieldSetting('default_format');
    $settings = $this->getSettings();
    // Generate faqfield id by fieldname and entity id.
    $faqfield_id = 'faqfield_' . $this->fieldDefinition->getName() . '_' . $items->getEntity()
      ->getEntityTypeId() . '_' . $items->getEntity()->id();
    // If active setting was blank, set FALSE so no element will be active.
    if (!is_numeric($settings['active'])) {
      $settings['active'] = FALSE;
    }
    $element_items = [];
    foreach ($items as $item) {
      // Decide whether to use the default format or the custom one.
      $format = (!empty($item->answer_format) ? $item->answer_format : $default_format);
      $element_items[] = [
        'question' => $item->question,
        'answer' => $item->answer,
        'answer_format' => $format,
      ];
    }
    if ($element_items) {
      $elements[0] = [
        '#theme' => 'faqfield_jquery_accordion_formatter',
        '#items' => $element_items,
        '#id' => $faqfield_id,
        '#attached' => [
          // Add FAQ Field accordion library.
          'library' => [
            'faqfield/faqfield.accordion',
          ],
          'drupalSettings' => [
            'faqfield' => [
              '#' . $faqfield_id => $settings,
            ],
          ],
        ],
      ];
    }

    return $elements;
  }

}
