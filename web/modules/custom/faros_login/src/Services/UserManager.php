<?php

declare(strict_types=1);

namespace Drupal\faros_login\Services;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
 * Manages user creation and operations.
 */
final class UserManager {

  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager
  ) {}

  /**
   * Builds the user values array.
   *
   * @param array $values
   *   An array of values for the user.
   *
   * @return array
   *   An array of user field values.
   */
  protected function buildUserValuesArray(array $values, EntityInterface $company): array {
    $user_fields = [
      'field_full_name' => $values['user_name'],
      'name' => $values['user_email'],
      'mail' => $values['user_email'],
      'pass' => $values['user_password'],
      'status' => 0,
      'roles' => ['authenticated'],
      'field_company' => $company->id(),
    ];

    return $user_fields;
  }

  /**
   * Creates a new user.
   *
   * @param array $values
   *   An array of values for the user.
   * @param EntityInterface $company
   *   The company entity to associate with the user.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The created user entity.
   */
  public function createUser(array $values, EntityInterface $company): EntityInterface {
    $fields = $this->buildUserValuesArray($values, $company);
    $user = $this->entityTypeManager->getStorage('user')->create($fields);
    $user->save();
    return $user;
  }

  public function activateUser(User $user) {
    $user->set('status', 1);
    $user->save();
  }

}
