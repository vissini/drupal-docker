<?php

declare(strict_types=1);

namespace Drupal\faros_login;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\faros_login\Form\CompanyFilterForm;

/**
 * Provides a list controller for the company entity type.
 */
final class CompanyListBuilder extends EntityListBuilder {



  /**
   * {@inheritdoc}
   */
  public function load() {
    // Get the parameter from the URL.
    $cnpj = \Drupal::request()->query->get('cnpj');

    // Modify the query to filter by CNPJ.
    $query = $this->getStorage()->getQuery()->accessCheck(TRUE);;

    if (!empty($cnpj)) {
      $query->condition('cnpj', '%' . $this->dbEscapeLike($cnpj) . '%', 'LIKE');
    }

    $entity_ids = $query->execute();

    return $this->storage->loadMultiple($entity_ids);
  }

  /**
   * Escape LIKE search.
   */
  protected function dbEscapeLike($string) {
    return str_replace(['%', '_'], ['\\%', '\\_'], $string);
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['id'] = $this->t('ID');
    $header['status'] = $this->t('Status');
    $header['cnpj'] = $this->t('CNPJ');
    $header['company_name'] = $this->t('Company Name');
    $header['created'] = $this->t('Created');
    $header['changed'] = $this->t('Updated');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\faros_login\CompanyInterface $entity */
    $row['id'] = $entity->toLink();
    $row['status'] = $entity->get('status')->value ? $this->t('Enabled') : $this->t('Disabled');
    $row['cnpj'] = $entity->get('cnpj')->value ?? '';
    $row['company_name'] = $entity->get('company_name')->value ?? '';
    $row['created']['data'] = $entity->get('created')->view(['label' => 'hidden']);
    $row['changed']['data'] = $entity->get('changed')->view(['label' => 'hidden']);
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    // Add the filter form above the listing.
    $build['filter_form'] = \Drupal::formBuilder()->getForm(CompanyFilterForm::class);
    $build['content'] = parent::render();
    return $build;
  }
}
