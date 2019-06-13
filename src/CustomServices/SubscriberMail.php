<?php

namespace Drupal\mailer\CustomServices;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\Core\Language\Language;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @class
 * SubscriberMail.
 */
class SubscriberMail {

  use StringTranslationTrait;

  /**
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  protected $campaign;
  protected $campaignTranslation;
  protected $campaignLang;
  protected $emailContent;
  protected $files = [];
  protected $mailConfig = [];

  /**
   * Constructor.
   */
  public function __construct(ConfigFactory $configFactory, EntityTypeManagerInterface $entityManager) {
    $this->configFactory = $configFactory;
    $this->entityManager = $entityManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Set the campaign that will be used to generate emails.
   */
  public function setCampaign(NodeInterface $node, $lang = FALSE) {
    $campaign_id = $node->Id();

    // Lang is passed in for test emails.
    if ($lang) {
      $campaign_lang = $lang;
    }
    else {
      $campaign_lang = $node->language()->getId();
    }

    $this->campaignLang = $campaign_lang;
    $this->campaign = $this->entityManager->getStorage('node')->load($campaign_id);
    $this->files = $this->setupFiles($campaign_id);
  }

  /**
   * Helper function to load all email config.
   */
  private function loadMailConfig() {
    $config = $this->configFactory->get('mailer.settings');
    $this->mailConfig = $config->get();
  }

  /**
   * Generate and send emails
   */
  public function generateMail($send_test_email = FALSE) {

    $this->loadMailConfig();

    // Do something.

    return TRUE;
  }

}
