<?php

namespace Drupal\Tests\bootstrap_horizontal_tabs\Functional;

use Drupal\Tests\node\Traits\ContentTypeCreationTrait;
use Drupal\Tests\BrowserTestBase;
use Drupal\Core\Url;

/**
 * Tests that the module custom field work within node entities.
 *
 * @group bootstrap_horizontal_tabs
 */
class FieldTest extends BrowserTestBase {

  use ContentTypeCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'node',
    'field',
    'filter',
    'user',
    'text',
    'bootstrap_horizontal_tabs',
    'bootstrap_horizontal_tabs_test_content_type',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser([
      'create basic_page content',
      'edit own basic_page content',
      'administer nodes',
    ]);
    $this->drupalLogin($account);
  }

  /**
   * Test markup shows for field.
   */
  public function testNode() {
    $web_assert = $this->assertSession();
    $title = $this->randomString();
    $this->drupalGet(Url::fromRoute('node.add', ['node_type' => 'basic_page']));
    $this->getSession()->getPage()->fillField('title[0][value]', $title);
    $this->getSession()->getPage()->fillField('field_horizontal_tabs[0][header]', 'First tab header');
    $this->getSession()->getPage()->fillField('field_horizontal_tabs[0][body][value]', 'First tab body');
    $this->getSession()->getPage()->pressButton('Add another item');
    $this->getSession()->getPage()->fillField('field_horizontal_tabs[1][header]', 'Second tab header');
    $this->getSession()->getPage()->fillField('field_horizontal_tabs[1][body][value]', 'Second tab body');
    $this->getSession()->getPage()->findButton('Save')->submit();
    $web_assert->pageTextContains("First tab header");
    $web_assert->pageTextContains("First tab body");
    $this->clickLink('Second tab header');
    $web_assert->pageTextContains("Second tab body");
  }

}
