<?php

declare(strict_types=1);

namespace Drupal\faros_login;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a company entity type.
 */
interface CompanyInterface extends ContentEntityInterface, EntityChangedInterface {

}
