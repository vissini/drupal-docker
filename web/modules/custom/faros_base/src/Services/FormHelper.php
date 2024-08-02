<?php

declare(strict_types=1);

namespace Drupal\faros_base\Services;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * @todo Add class description.
 */
final class FormHelper {

  use StringTranslationTrait;

  /**
   * Creates a textfield element for the form.
   *
   * @param string $title
   *   The title of the textfield.
   * @param string $name
   *   The name of the textfield.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   * @param bool $required
   *   (optional) Whether the textfield is required. Defaults to TRUE.
   *
   * @return array
   *   An array representing the textfield element.
   */
  public function createTextField(string $title, string $name, FormStateInterface $form_state, bool $required = TRUE): array {
    return [
      '#type' => 'textfield',
      '#title' => $this->t($title),
      '#required' => $required,
      '#default_value' => $form_state->getValue($name, ''),
    ];
  }

  /**
   * Creates a password field element.
   *
   * @param string $title
   *   The title of the password field.
   * @param string $name
   *   The name of the password field.
   *
   * @return array
   *   An array representing the password field element.
   */
  public function createPasswordField(string $title, string $name, bool $addSuffix = FALSE): array {
    $field = [
      '#type' => 'password',
      '#title' => $this->t($title),
      '#required' => TRUE,
    ];

    if ($addSuffix) {
      $field['#suffix'] = '<span class="toggle-password-requirements">' . $this->t('Requirements') . '</span>';
    }

    return $field;
  }

  /**
   * Creates a telephone field element for the form.
   *
   * @param string $title
   *   The title of the field.
   * @param string $name
   *   The name of the field.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   * @param bool $required
   *   (optional) Whether the field is required. Defaults to TRUE.
   *
   * @return array
   *   An array representing the telephone field element.
   */
  public function createTelephoneField(string $title, string $name, FormStateInterface $form_state, bool $required = TRUE): array {
    return [
      '#type' => 'tel',
      '#title' => $this->t($title),
      '#required' => $required,
      '#default_value' => $form_state->getValue($name, ''),
      '#attributes' => [
        'class' => ['telephone-mask'],
      ],
    ];
  }

  /**
   * Creates an email field element for the form.
   *
   * @param string $title
   *   The title of the email field.
   * @param string $name
   *   The name of the email field.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return array
   *   An array representing the email field element.
   */
  public function createEmailField(string $title, string $name, FormStateInterface $form_state): array {
    return [
      '#type' => 'email',
      '#title' => $this->t($title),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue($name, ''),
    ];
  }

  /**
   * Creates a select field element.
   *
   * @param string $title
   *   The title of the select field.
   * @param string $name
   *   The name of the select field.
   * @param array $options
   *   An array of options for the select field.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param bool $required
   *   (optional) Whether the select field is required. Defaults to TRUE.
   *
   * @return array
   *   An array representing the select field element.
   */
  public function createSelectField(string $title, string $name, array $options, FormStateInterface $form_state, bool $required = TRUE): array {
    return [
      '#type' => 'select',
      '#title' => $this->t($title),
      '#options' => $options,
      '#required' => $required,
      '#default_value' => $form_state->getValue($name, ''),
      '#empty_value' => '',
    ];
  }

   /**
   * Creates a checkboxes form field.
   *
   * @param string $title
   *   The title of the checkboxes field.
   * @param string $name
   *   The name of the checkboxes field.
   * @param array $options
   *   An array of options for the checkboxes field.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   An array representing the checkboxes field.
   */
  public function createCheckboxesField(string $title, string $name, array $options, FormStateInterface $form_state): array {
    return [
      '#type' => 'checkboxes',
      '#title' => $this->t($title),
      '#options' => $options,
      '#default_value' => $form_state->getValue($name, []),
    ];
  }

  /**
   * Creates a single checkbox form field.
   *
   * @param string $title The title of the checkbox field.
   * @param string $name The name of the checkbox field.
   * @param bool $required Determines if the checkbox field is required.
   * @param FormStateInterface $form_state The form state object.
   * @param string $description (optional) The description of the checkbox field.
   * @return array The checkbox field array.
   */
  public function createSingleCheckboxField(string $title, string $name, bool $required, FormStateInterface $form_state, string $description = ''): array {
    return [
      '#type' => 'checkbox',
      '#title' => $this->t($title),
      '#description' => $this->t($description),
      '#required' => $required,
      '#default_value' => $form_state->getValue($name, 0),
    ];
  }
  

}
