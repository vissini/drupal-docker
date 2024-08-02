<?php

declare(strict_types=1);

namespace Drupal\faros_login\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\faros_base\Services\ContentLoaderService;
use Drupal\faros_base\Services\FormHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Faros Login settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * Constructs a new SettingsForm object.
   *
   * @param \Drupal\faros_base\Services\ContentLoaderService $content_loader
   *   The content loader service.
   * @param \Drupal\faros_base\Services\FormHelper $form_helper
   *   The form helper service.
   */
  public function __construct(protected ContentLoaderService $content_loader, protected FormHelper $form_helper) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('faros_base.content_loader_service'),
      $container->get('faros_base.form_helper')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'faros_login_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['faros_login.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('faros_login.settings');

    // Obter os planos como opções
    $plan_options = $this->content_loader->getContentTypeOptions('plans', 'title', true, 'Select a plan');

    $form['default_plan'] = $this->form_helper->createSelectField(
      'Default Plan',
      'default_plan',
      $plan_options,
      $form_state,
      true
    );
    $form['default_plan']['#default_value'] = $config->get('default_plan');
    $form['default_plan']['#description'] = $this->t('Select the default plan.');

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('faros_login.settings')
      ->set('default_plan', $form_state->getValue('default_plan'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
