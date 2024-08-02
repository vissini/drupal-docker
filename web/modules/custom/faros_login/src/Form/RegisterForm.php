<?php

declare(strict_types=1);

namespace Drupal\faros_login\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\faros_base\Services\ContentLoaderService;
use Drupal\faros_base\Services\FormHelper;
use Drupal\faros_login\Services\EmailManager;
use Drupal\faros_login\Services\CompanyManager;
use Drupal\faros_login\Services\RegisterFormValidations;
use Drupal\faros_login\Services\UserManager;
use Drupal\taxonomy\TermStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Faros Login form.
 */
final class RegisterForm extends FormBase {


  const VOCABULARY_COMPANY_SECTOR = 'company_sector';
  const VOCABULARY_COMPANY_TYPE = 'company_type';
  const VOCABULARY_COMPANY_INTEREST = 'company_interest';

  const STEP_USER_REGISTRATION = 'user_registration';
  const STEP_COMPANY_REGISTRATION = 'company_registration';
  const STEP_CONFIRMATION = 'confirmation';

  /**
   * Constructs a new RegisterForm object.
   *
   * @param \Drupal\taxonomy\TermStorageInterface $term_storage
   *   The term storage service.
   */
  public function __construct(
    protected FormHelper $form_helper,
    protected RegisterFormValidations $validations,
    protected ContentLoaderService $content_loader,
    protected CompanyManager $company_manager,
    protected UserManager $user_manager,
    protected EmailManager $email_manager
    ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('faros_base.form_helper'),
      $container->get('faros_login.register_form_validations'),
      $container->get('faros_base.content_loader_service'),
      $container->get('faros_login.company_manager'),
      $container->get('faros_login.user_manager'),
      $container->get('faros_login.email_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'faros_login_register';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $this->attachLibraries($form);
    $step = $this->getCurrentStep($form, $form_state);
    $this->restoreFormValues($form_state);

    $form['progress'] = $this->buildProgressBar($step);

    switch ($step) {
      case self::STEP_USER_REGISTRATION:
        $form = $this->buildUserRegistrationForm($form, $form_state);
        break;
      case self::STEP_COMPANY_REGISTRATION:
        $form = $this->buildCompanyRegistrationForm($form, $form_state);
        break;
      case self::STEP_CONFIRMATION:
        $form = $this->buildConfirmationForm($form, $form_state);
        break;
    }

    $form['actions'] = $this->buildFormActions($step);
    return $form;
  }

  /**
   * Attaches libraries to the form.
   *
   * @param array $form
   *   The form array.
   */
  private function attachLibraries(array &$form): void {
    $form['#attached']['library'][] = 'faros_login/telephone_mask';
    $form['#attached']['library'][] = 'faros_login/faros_login';
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
  }

  /**
   * Restores form values from storage.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  private function restoreFormValues(FormStateInterface $form_state): void {
    $storage = $form_state->getStorage();
    if (!empty($storage['multistep'])) {
      foreach ($storage['multistep'] as $key => $value) {
        $form_state->setValue($key, $value);
      }
    }
  }

  /**
   * Gets the current step of the form.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return string
   *   The current step of the form.
   */
  private function getCurrentStep(array &$form, FormStateInterface $form_state): string {
    $step = $form_state->get('step') ?? self::STEP_USER_REGISTRATION;
    $form_state->set('step', $step);

    $form['step'] = [
      '#type' => 'value',
      '#value' => $step,
    ];

    return $step;
  }

  /**
   * Builds the progress bar for the registration form.
   *
   * @param string $current_step
   *   The current step of the form.
   *
   * @return array
   *   The progress bar render array.
   */
  private function buildProgressBar(string $current_step): array {
    $steps = [
      self::STEP_USER_REGISTRATION => [
        'title' => $this->t('User Registration'),
        'number' => '1',
      ],
      self::STEP_COMPANY_REGISTRATION => [
        'title' => $this->t('Company Registration'),
        'number' => '2',
      ],
      self::STEP_CONFIRMATION => [
        'title' => $this->t('Confirmation'),
        'number' => '3',
      ],
    ];

    $progress_bar = [
      '#type' => 'container',
      '#attributes' => ['class' => ['progress-bar']],
    ];

    foreach ($steps as $step => $info) {
      $is_active = ($step === $current_step) ? ' active' : '';
      $progress_bar[$step] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['progress-step' . $is_active]],
        'number' => [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#value' => $info['number'],
          '#attributes' => ['class' => ['step-number']],
        ],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#value' => $info['title'],
          '#attributes' => ['class' => ['step-title']],
        ],
      ];
    }

    return $progress_bar;
  }

  /**
   * Builds the user registration form.
   *
   * This method is responsible for building the form elements for user registration.
   *
   * @param array &$form
   *   The form array to which the form elements will be added.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The updated form array.
   */
  private function buildUserRegistrationForm(array &$form, FormStateInterface $form_state): array {
    $form_helper = $this->form_helper;
    $form['user_name'] = $form_helper->createTextField('Name', 'user_name', $form_state);
    $form['user_lastname'] = $form_helper->createTextField('Lastname', 'user_lastname', $form_state);
    $form['user_password'] = $form_helper->createPasswordField('Password', 'user_password', TRUE);
    $form['user_password_confirmation'] = $form_helper->createPasswordField('Password Confirmation', 'user_password_confirmation');
    $form['password_requirements'] = $this->createPasswordRequirementsMarkup();
    $form['user_telephone'] = $form_helper->createTelephoneField('Telephone', 'user_telephone', $form_state);
    $form['user_whatsapp'] = $form_helper->createTelephoneField('WhatsApp', 'user_whatsapp', $form_state, FALSE);
    $form['user_email'] = $form_helper->createEmailField('Email', 'user_email', $form_state);
    $form['user_agreement'] = $this->createAgreementCheckbox($form_state);

    return $form;
  }

  /**
   * Builds the company registration form.
   *
   * @param array $form
   *   An associative array containing the form structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The built form structure.
   */
  private function buildCompanyRegistrationForm(array &$form, FormStateInterface $form_state): array {
    $form_helper = $this->form_helper;
    $form['company_name'] = $form_helper->createTextField('Company Name', 'company_name', $form_state);
    $form['cnpj'] = $form_helper->createTextField('CNPJ', 'cnpj', $form_state);

    $company_type_options = $this->content_loader->getTaxonomyTermOptions(self::VOCABULARY_COMPANY_TYPE, true, 'Select Institution');
    $form['company_type'] = $form_helper->createSelectField(
      'Company Type', 
      'company_type', 
      $company_type_options, 
      $form_state
    );

    $company_sector_options = $this->content_loader->getTaxonomyTermOptions(self::VOCABULARY_COMPANY_SECTOR, true, 'Activity Sector');
    $form['company_sector'] = $form_helper->createSelectField(
      'Company Sector', 
      'company_sector', 
      $company_sector_options, 
      $form_state
    );

    $form['user_position'] = $form_helper->createTextField('Position', 'user_position', $form_state);

    $company_interest_options = $this->content_loader->getTaxonomyTermOptions(self::VOCABULARY_COMPANY_INTEREST, false);
    $form['company_interest'] = $form_helper->createCheckboxesField(
      'Company Interest', 
      'company_interest', 
      $company_interest_options, 
      $form_state
    );

    return $form;
  }

  /**
   * Builds the confirmation form.
   *
   * @param array $form
   *   The form array.
   *
   * @return array
   *   The built form structure.
   */
  private function buildConfirmationForm(array &$form): array {
    $form['confirmation_message'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Thank you for registering. Your form has been successfully submitted.'),
    ];
    return $form;
  }

  /**
   * Creates the password requirements markup.
   *
   * @return array
   *   The password requirements markup.
   */
  private function createPasswordRequirementsMarkup() {
    $markup = '<div id="password-requirements" style="display:none;">' . $this->validations->getPasswordRequirementsList() . '</div>';
  
    // Mark the markup as safe
    $safe_markup = Markup::create($markup);
  
    return [
      '#type' => 'markup',
      '#markup' => $safe_markup,
    ];
  }

  /**
   * Creates the agreement checkbox element for the registration form.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return array
   *   An array representing the agreement checkbox element.
   */
  private function createAgreementCheckbox(FormStateInterface $form_state): array {
    return $this->form_helper->createSingleCheckboxField(
      'I agree to the terms and privacy policy',
      'user_agreement',
      TRUE,
      $form_state
    );
  }

  /**
   * Builds the form actions based on the given step.
   *
   * @param string $step
   *   The current step of the registration process.
   *
   * @return array
   *   An array of form actions.
   */
  private function buildFormActions(string $step): array {
    switch ($step) {
      case self::STEP_USER_REGISTRATION:
        return $this->buildUserRegistrationActions();
      case self::STEP_COMPANY_REGISTRATION:
        return $this->buildCompanyRegistrationActions();
      case self::STEP_CONFIRMATION:
        return $this->buildConfirmationActions();
      default:
        return [];
    }
  }

  /**
   * Builds the actions for the user registration step.
   *
   * @return array
   *   An array containing the actions.
   */
  private function buildUserRegistrationActions(): array {
    return [
      '#type' => 'actions',
      'next' => [
        '#type' => 'submit',
        '#value' => $this->t('Iniciar Cadastro'),
        '#submit' => ['::nextSubmit'],
        '#attributes' => ['class' => ['button--primary']],
        '#states' => $this->getNextButtonStates(),
      ],
    ];
  }

  /**
   * Builds the actions for the company registration step.
   *
   * @return array
   *   An array containing the actions.
   */
  private function buildCompanyRegistrationActions(): array {
    return [
      '#type' => 'actions',
      'previous' => [
        '#type' => 'submit',
        '#value' => $this->t('Voltar Etapa'),
        '#submit' => ['::previousSubmit'],
        '#limit_validation_errors' => [],
      ],
      'next' => [
        '#type' => 'submit',
        '#value' => $this->t('Finalizar Cadastro'),
        '#submit' => ['::nextSubmit'],
        '#attributes' => ['class' => ['button--primary']],
        '#states' => $this->getNextButtonStates(),
      ],
    ];
  }

  /**
   * Builds the actions for the confirmation step.
   *
   * @return array
   *   An array containing the actions.
   */
  private function buildConfirmationActions(): array {
    return [
      '#type' => 'actions',
      'finish' => [
        '#type' => 'markup',
        '#markup' => $this->t('Your registration is complete.'),
      ],
    ];
  }

  /**
   * Gets the next button states for the register form.
   *
   * @return array
   *   An array of button states.
   */
  private function getNextButtonStates(): array {
    return [
      'enabled' => [
        ':input[name="user_agreement"]' => ['checked' => TRUE],
      ],
    ];
  }

  /**
   * Handles the submission of the form when the "Next" button is clicked.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @throws \Drupal\Core\Form\FormValidationException
   *   Thrown when the form validation fails.
   */
  public function nextSubmit(array &$form, FormStateInterface $form_state): void {
    $this->storeFormValues($form_state);

    $current_step = $form_state->get('step');

    switch ($current_step) {
      case self::STEP_USER_REGISTRATION:
        $form_state->set('step', self::STEP_COMPANY_REGISTRATION);
        break;
      case self::STEP_COMPANY_REGISTRATION:
        $this->submitForm($form, $form_state);
        $form_state->set('step', self::STEP_CONFIRMATION);
        break;
      case self::STEP_CONFIRMATION:
        break;
      default:
        throw new \UnexpectedValueException("Invalid step: $current_step");
    }

    $form_state->setRebuild(TRUE);
  }

  /**
   * Handles the submission of the previous button in the register form.
   *
   * This method stores the form values, sets the current step to 'user_registration',
   * and triggers a form rebuild.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return void
   */
  public function previousSubmit(array &$form, FormStateInterface $form_state): void {
    $this->storeFormValues($form_state);
    $form_state->set('step', self::STEP_USER_REGISTRATION);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Stores the form values in the form state storage.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return void
   *   No return value.
   */
  private function storeFormValues(FormStateInterface $form_state): void {
    $values = $form_state->getValues();
    $storage = $form_state->getStorage();
    foreach ($values as $key => $value) {
      $storage['multistep'][$key] = $value;
    }
    $form_state->setStorage($storage);
  }

  /**
   * Validates the form submission.
   *
   * @param array &$form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @throws \Drupal\Core\Form\FormValidationException
   *   Thrown when the form validation fails.
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $current_step = $form_state->get('step');
    if ($current_step === self::STEP_USER_REGISTRATION) {
      $this->validations->validatePasswordConfirmation($form_state);
      $this->validations->validatePasswordComplexity($form_state);
      $this->validations->validateAgreement($form_state);
      $this->validations->validateUniqueEmail($form_state);
    }

    if ($current_step === self::STEP_COMPANY_REGISTRATION) {
      $this->validations->validateUniqueCnpj($form_state);
    }
  }

  /**
   * Form submission handler for the RegisterForm form.
   *
   * @param array &$form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   Thrown when there is an error saving the entity.
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $values = $form_state->getStorage()['multistep'];

    $company = $this->company_manager->createCompany($values);
    $user = $this->user_manager->createUser($values, $company);
    $this->email_manager->sendActivationEmail($user, $company);
  }
}
