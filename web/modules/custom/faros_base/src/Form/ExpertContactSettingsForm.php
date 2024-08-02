<?php

namespace Drupal\faros_base\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure expert contact settings for this site.
 */
class ExpertContactSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'expert_contact.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'expert_contact_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('faros_base.expert_contact_settings');

    $form['phone_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Whatsapp'),
      '#description' => $this->t('Adicionar no formato: +55999999999'),
      '#default_value' => $config->get('phone_number'),
      '#required' => TRUE,
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Texto da mensagem'),
      '#default_value' => $config->get('message'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('faros_base.expert_contact_settings')
      ->set('phone_number', $form_state->getValue('phone_number'))
      ->set('message', $form_state->getValue('message'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
