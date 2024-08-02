<?php

declare(strict_types=1);

namespace Drupal\faros_login\Services;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\Url;

/**
 * @todo Add class description.
 */
final class EmailManager {

  /**
   * Constructs an EmailManager object.
   */
  public function __construct(
    private readonly MailManagerInterface $pluginManagerMail,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly PrivateTempStoreFactory  $tempStoreFactory,
  ) {}

  /**
   * Sends an activation email to the user.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user object.
   * @param \Drupal\company\Entity\CompanyInterface $company
   *   The company object.
   *
   * @return bool
   *   TRUE if the email was sent successfully, FALSE otherwise.
   */
  public function sendActivationEmail($user, $company) {
    $module = 'faros_login';
    $key = 'activation_email';
    $to = $user->getEmail();
    $from = $this->configFactory->get('system.site')->get('mail');
    $params = [
      'subject' => t('Activate your account'),
      'body' => t('Click the link to activate your account and company: @url', [
        '@url' => Url::fromRoute('faros_login.activate', ['uid' => $user->id(), 'hash' => $this->generateActivationHash($user, $company)], ['absolute' => TRUE])->toString(),
      ]),
    ];
    $langcode = $user->getPreferredLangcode();
    $send = TRUE;

    $result = $this->pluginManagerMail->mail($module, $key, $to, $langcode, $params, $from, $send);

    return $result['result'];
  }

  protected function generateActivationHash($user, $company) {
    return hash('sha256', $user->id() . $user->getEmail() . $company->id() . $this->tempStoreFactory->get('faros_login')->get('salt'));
  }

  public function isValidHash($user, $company, $hash) {
    $expectedHash = $this->generateActivationHash($user, $company);
    return $hash === $expectedHash;
  }

}
