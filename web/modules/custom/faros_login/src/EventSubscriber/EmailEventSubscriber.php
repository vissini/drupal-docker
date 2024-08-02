<?php

declare(strict_types=1);

namespace Drupal\faros_login\EventSubscriber;

// use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\core_event_dispatcher\Event\Core\MailAlterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @todo Add description for this subscriber.
 */
final class EmailEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      // CoreHookEvents::MAIL_ALTER => 'onMailAlter',
    ];
  }

  /**
   * Alters the email.
   *
   * @param \Drupal\hook_event_dispatcher\Event\Mail\MailAlterEvent $event
   *   The mail alter event.
   */
  public function onMailAlter(MailAlterEvent $event) {
    $message = &$event->getMessage();

    switch ($message['id']) {
      case 'faros_login_activation_email':
        $message['subject'] = $message['params']['subject'];
        $message['body'][] = $message['params']['body'];
        break;
    }
  }
}
