<?php

declare(strict_types=1);

namespace Drupal\faros_login\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class RegisterFormValidations
 *
 * This class provides validation methods for the registration form.
 */
final class RegisterFormValidations {

  use StringTranslationTrait;

  /**
   * Constructs a new RegisterFormValidations object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager
  ) {}

  /**
   * Validates the password confirmation.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function validatePasswordConfirmation(FormStateInterface $form_state): void {
    $password = $form_state->getValue('user_password') ?? '';
    $password_confirmation = $form_state->getValue('user_password_confirmation') ?? '';
    if ($password !== $password_confirmation) {
      $form_state->setErrorByName('user_password_confirmation', t('Password confirmation does not match.'));
    }
  }

  /**
   * Validates the complexity of the password.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function validatePasswordComplexity(FormStateInterface $form_state): void {
    $password = $form_state->getValue('user_password') ?? '';
    if (!preg_match($this->getPasswordValidationRegex(), $password)) {
      $form_state->setErrorByName('user_password', t('Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&).'));
    }
  }

  /**
   * Validates the user agreement field in the registration form.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function validateAgreement(FormStateInterface $form_state): void {
    if (empty($form_state->getValue('user_agreement'))) {
      $form_state->setErrorByName('user_agreement', t('You must agree to the terms and privacy policy.'));
    }
  }

  /**
   * Returns the password validation regular expression.
   *
   * @return string The password validation regular expression.
   */
  private function getPasswordValidationRegex(): string {
    return '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
  }

  /**
   * Returns the password requirements list as a translated HTML string.
   *
   * @return string
   *   The password requirements list.
   */
  public function getPasswordRequirementsList() {
    $requirements = [
      $this->t('At least 8 characters'),
      $this->t('One uppercase letter'),
      $this->t('One lowercase letter'),
      $this->t('One number'),
      $this->t('One special character (@$!%*?&)'),
    ];

    $list = '<ul>';
    foreach ($requirements as $requirement) {
      $list .= '<li>' . $requirement . '</li>';
    }
    $list .= '</ul>';

    return $list;
  }

  /**
   * Validates if the CNPJ (Cadastro Nacional da Pessoa Jurídica) is unique.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function validateUniqueCnpj(FormStateInterface $form_state) {
    $cnpj = $form_state->getValue('cnpj');

    $query = $this->entityTypeManager->getStorage('faros_login_company')
      ->getQuery()
      ->condition('cnpj', $cnpj)
      ->accessCheck(TRUE)
      ->range(0, 1);

    $result = $query->execute();

    if (!empty($result)) {
      $form_state->setErrorByName('cnpj', t('The CNPJ already exists.'));
    }
  }

  /**
   * Validates if the email entered in the registration form is unique.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function validateUniqueEmail(FormStateInterface $form_state) {
    $email = $form_state->getValue('user_email');

    $query = $this->entityTypeManager->getStorage('user')
      ->getQuery()
      ->condition('mail', $email)
      ->accessCheck(TRUE)
      ->range(0, 1);

    $result = $query->execute();

    if (!empty($result)) {
      $form_state->setErrorByName('user_email', t('The email is already registered.'));
    }
  }

}
