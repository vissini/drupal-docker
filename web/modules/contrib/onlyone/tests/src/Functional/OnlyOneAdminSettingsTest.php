<?php

namespace Drupal\Tests\onlyone\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test the onlyone_admin_settings configuration form.
 *
 * @group onlyone
 */
class OnlyOneAdminSettingsTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['onlyone'];

  /**
   * Tests the configuration form, the permission and the link.
   */
  public function testConfigurationForm() {
    // Going to the config page.
    $this->drupalGet('/admin/config/content/onlyone/settings');

    // Checking that the page is not accesible for anonymous users.
    $this->assertSession()->statusCodeEquals(403);

    // Creating a user with the module permission.
    $account = $this->drupalCreateUser(['administer onlyone', 'access administration pages']);
    // Log in.
    $this->drupalLogin($account);

    // Checking the module link.
    $this->drupalGet('/admin/config/content');
    $this->assertSession()->linkByHrefExists('/admin/config/content/onlyone');

    // @TODO Check the module local task.
    // Going to the config page.
    $this->drupalGet('/admin/config/content/onlyone/settings');
    // Checking that the request has succeeded.
    $this->assertSession()->statusCodeEquals(200);

    // Checking the page title.
    $this->assertSession()->elementTextContains('css', 'h1', 'Only One Settings');
    // Check that the checkbox is unchecked.
    $this->assertSession()->checkboxNotChecked('onlyone_new_menu_entry');

    // Form values to send (checking check checkbox).
    $edit = [
      'onlyone_new_menu_entry' => 1,
      'onlyone_redirect' => 1,
    ];
    // Sending the form.
    $this->submitForm($edit, 'op');
    // Verifying the save message.
    $this->assertSession()->pageTextContains('The configuration options have been saved.');

    // Getting the config factory service.
    $config_factory = $this->container->get('config.factory');

    // Getting variables.
    $onlyone_new_menu_entry = $config_factory->get('onlyone.settings')->get('onlyone_new_menu_entry');
    $onlyone_redirect = $config_factory->get('onlyone.settings')->get('onlyone_redirect');

    // Verifying that the config values are stored.
    $this->assertTrue($onlyone_new_menu_entry, 'The configuration value for onlyone_new_menu_entry should be TRUE.');
    $this->assertTrue($onlyone_redirect, 'The configuration value for onlyone_redirect should be TRUE.');

    // Form values to send (checking uncheck checkbox).
    $edit = [
      'onlyone_new_menu_entry' => 0,
      'onlyone_redirect' => 0,
    ];
    // Sending the form.
    $this->submitForm($edit, 'op');

    // Getting variables.
    $onlyone_new_menu_entry = $config_factory->get('onlyone.settings')->get('onlyone_new_menu_entry');
    $onlyone_redirect = $config_factory->get('onlyone.settings')->get('onlyone_redirect');

    // Verifying that the config values are stored.
    $this->assertFalse($onlyone_new_menu_entry, 'The configuration value for onlyone_new_menu_entry should be FALSE.');
    $this->assertFalse($onlyone_redirect, 'The configuration value for onlyone_redirect should be FALSE.');
  }

}
