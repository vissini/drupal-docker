<?php

declare(strict_types=1);

namespace Drupal\faros_login\Services;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\faros_login\Entity\Company;

/**
 * @todo Add class description.
 */
final class CompanyManager {

  const COMPANY_STATUS_ACTIVE = 'active';
  const COMPANY_STATUS_INACTIVE = 'inactive';
  const COMPANY_STATUS_SUSPENDED = 'suspended';

  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected ConfigFactoryInterface $configFactory
  ) {}


  protected function buildCompanyValuesArray($values): array {

    $config = $this->configFactory->get('faros_login.settings');
    $defaulPlan = $config->get('default_plan');

    $company_fields = [
      'cnpj' => $values['cnpj'],
      'company_name' => $values['company_name'],
      'company_type' => $values['company_type'],
      'company_sector' => $values['company_sector'],
      'company_interest' => $values['company_interest'],
      'plan' => $defaulPlan,
    ];

    return $company_fields;
  }

  public function createCompany(array $values):EntityInterface {
    $fields = $this->buildCompanyValuesArray($values);
    $company = $this->entityTypeManager->getStorage('faros_login_company')->create($fields);
    $company->save();    
    return $company;
  }

  public function loadByUser($user) {
    $company_id = $user->get('field_company')->target_id;
    return $this->entityTypeManager->getStorage('faros_login_company')->load($company_id);
  }

  public function activateCompany(Company $company) {
    $company->set('status', self::COMPANY_STATUS_ACTIVE);
    $company->save();
  }

}
