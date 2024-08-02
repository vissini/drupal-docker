<?php

declare(strict_types=1);

namespace Drupal\faros_login\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\faros_login\CompanyInterface;

/**
 * Defines the company entity class.
 *
 * @ContentEntityType(
 *   id = "faros_login_company",
 *   label = @Translation("Company"),
 *   label_collection = @Translation("Companies"),
 *   label_singular = @Translation("company"),
 *   label_plural = @Translation("companies"),
 *   label_count = @PluralTranslation(
 *     singular = "@count companies",
 *     plural = "@count companies",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\faros_login\CompanyListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\faros_login\Form\CompanyForm",
 *       "edit" = "Drupal\faros_login\Form\CompanyForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "faros_login_company",
 *   admin_permission = "administer faros_login_company",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/company",
 *     "add-form" = "/company/add",
 *     "canonical" = "/company/{faros_login_company}",
 *     "edit-form" = "/company/{faros_login_company}/edit",
 *     "delete-form" = "/company/{faros_login_company}/delete",
 *     "delete-multiple-form" = "/admin/content/company/delete-multiple",
 *   },
 *   field_ui_base_route = "entity.faros_login_company.settings",
 * )
 */
final class Company extends ContentEntityBase implements CompanyInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['cnpj'] = BaseFieldDefinition::create('string')
      ->setLabel(t('CNPJ'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 20)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['company_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Company Name'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['company_sector'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Company Sector'))
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings', ['target_bundles' => ['company_sector']])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_select',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'entity_reference_label',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['company_type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Company Type'))
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings', ['target_bundles' => ['company_type']])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_select',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'entity_reference_label',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['company_interest'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Company Interest'))
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings', ['target_bundles' => ['company_interest']])
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
        'weight' => -1,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 'medium',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'entity_reference_label',
        'weight' => -1,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['plan'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Plan'))
      ->setSetting('target_type', 'node')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings', ['target_bundles' => ['plans']])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_select',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'entity_reference_label',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['plan_start_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Plan Start Date'))
      ->setDisplayOptions('form', [
        'type' => 'datetime_date',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'datetime_date',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('view', TRUE);


    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the company was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the company was last edited.'));

    $fields['status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Status'))
      ->setRequired(TRUE)
      ->setSettings([
        'allowed_values' => [
          'active' => t('Active'),
          'inactive' => t('Inactive'),
          'suspended' => t('Suspended'),
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'list_default',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }
}
