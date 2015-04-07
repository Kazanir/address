<?php

/**
 * @file
 * Contains \Drupal\address\Form\ZoneMemberDeleteForm.
 */

namespace Drupal\address\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\address\ZoneMemberInterface;
use Drupal\address\Entity\Zone;
use CommerceGuys\Zone\Model\ZoneInterface;

/**
 * Form for deleting a zone member.
 */
class ZoneMemberDeleteForm extends ConfirmFormBase {

  /**
   * The zone entity containing the zone member to be deleted.
   *
   * @var \CommerceGuys\Zone\Model\ZoneInterface;
   */
  protected $zone;

  /**
   * The zone member to be deleted.
   *
   * @var \Drupal\address\ZoneMemberInterface
   */
  protected $zoneMember;

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the @member member from the %zone zone?', ['%zone' => $this->zone->label(), '@member' => $this->zoneMember->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->zone->urlInfo('edit-form');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'zone_member_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Zone $zone = NULL, ZoneMemberInterface $member = NULL) {
    $this->zone = $zone;
    $this->zoneMember = $this->zone->getMember($member);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->zone->deleteMember($this->member);
    drupal_set_message($this->t('The zone member %name has been deleted.', ['%name' => $this->zoneMember->label()]));
    $form_state->setRedirectUrl($this->zone->urlInfo('edit-form'));
  }

}

