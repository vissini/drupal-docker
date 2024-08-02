<?php

declare(strict_types=1);

namespace Drupal\faros_login\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\faros_login\Services\EmailManager;
use Drupal\faros_login\Services\CompanyManager;
use Drupal\faros_login\Services\UserManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\user\UserAuthInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\user\UserDataInterface;

/**
 * Returns responses for Faros Login routes.
 */
final class ActivationController extends ControllerBase {

  /**
   * The controller constructor.
   */
  public function __construct(
    private readonly UserManager $userManager,
    private readonly CompanyManager $companyManager,
    private readonly EmailManager $emailManager,
    private readonly UserAuthInterface $userAuth,
    private readonly AccountProxyInterface $currentUserService,
    private readonly RequestStack $requestStack,
    private readonly SessionManagerInterface $sessionManager,
    private readonly UserDataInterface $userData,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('faros_login.user_manager'),
      $container->get('faros_login.company_manager'),
      $container->get('faros_login.email_manager'),
      $container->get('user.auth'),
      $container->get('current_user'),
      $container->get('request_stack'),
      $container->get('session_manager'),
      $container->get('user.data'),
    );
  }

  public function activate($uid, $hash) {
    $user = $this->entityTypeManager()->getStorage('user')->load($uid);
    $company = $this->companyManager->loadByUser($user);

    if ($this->emailManager->isValidHash($user, $company, $hash)) {
      $this->userManager->activateUser($user);
      $this->companyManager->activateCompany($company);

      // Login the user
      $this->userAuth->authenticate($user->getAccountName(), $user->getPassword());
      $this->currentUserService->setAccount($user);
      $this->sessionManager->regenerate();
      $this->userData->set('session', $user->id(), 'csrf_token', base64_encode(random_bytes(32)));

      $this->messenger()->addStatus(t('Account and company activated successfully.'));
    } else {
      $this->messenger()->addError(t('Invalid activation link.'));
    }

    $url = Url::fromRoute('<front>');
    return new RedirectResponse($url->toString());
  }
}
