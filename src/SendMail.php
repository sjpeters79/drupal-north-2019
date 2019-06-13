<?php

namespace Drupal\mailer;

use Drupal\node\NodeInterface;
use Drupal\mailer\CustomServices\SubscriberMail;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Send mail controller.
 */
class SendMail extends ControllerBase {

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The Url route service.
   *
   * @var \Drupal\stc_campaign_ext_mailer\Plugin\SubscriberMail
   */
  protected $mailer;

  /**
   * Constructor.
   */
  public function __construct(SubscriberMail $mailer, MessengerInterface $messenger) {
    $this->mailer = $mailer;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('mailer.subscribermail'),
      $container->get('messenger'),
    );
  }

  /**
   * Sends test mail for this campaign.
   */
  public function sendTestMail(NodeInterface $node) {
    $lang = FALSE;
    $lang = $node->language()->getId();

    $this->mailer->setCampaign($node, $lang);
    $this->mailer->generateMail(TRUE);
    $this->messenger->addStatus($this->t('Test emails sent.'));

    $campaign = $this->entityManager->getStorage('node')->load($node->Id());
    $campaign = $campaign->getTranslation($campaign_lang);

    $request_time = date(DATETIME_DATETIME_STORAGE_FORMAT, time('now'));
    $campaign->field_last_test_run = $request_time;
    $campaign->save();

    // Redirect to front page.
    $url = Url::fromRoute('<front>')->toString();
    return new RedirectResponse($url);
  }

}
