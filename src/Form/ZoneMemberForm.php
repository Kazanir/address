<?php

/**
 * @file
 * Contains \Drupal\address\Form\ZoneMemberForm
 */

namespace Drupal\address\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CommerceGuys\Zone\Model\ZoneInterface;
use Drupal\address\Entity\Zone;
use Drupal\address\ZoneMemberManager;

class ZoneMemberForm extends FormBase {

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\address\Entity\Zone $zone
   *   The zone entity.
   * @param int $member_id
   *   The key of the zone member being edited.
   *
   * @return array
   *   The form structure.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function buildForm(array $form, FormStateInterface $formState, ZoneInterface $zone = NULL, $member_id = NULL) {
    $this->zone = $zone;
    $this->zoneMember = $this->zone->getMember($memberUuid);

    $request = $this->getRequest();

    $form['id'] = [
      '#type' => 'value',
      '#value' => $memberUuid,
    ];

    $form['plugin_id'] = [
      '#type' => 'value',
      '#value' => $this->zoneMember->getPluginId(),
    ];

    $form['data'] = $this->zoneMember->buildConfigurationForm([], $formState);
    $form['data']['#tree'] = TRUE;

    // Check the URL for a weight, then the zone member, otherwise use default.
    $form['weight'] = [
      '#type' => 'hidden',
      '#value' => $request->query->has('weight') ? (int) $request->query->get('weight') : $this->zoneMember->getWeight(),
    ];
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
    ];
    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#url' => $this->zone->urlInfo('edit-form'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'zone_member_form';
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // The zone member configuration is stored in the 'data' key in the form,
    // pass that through for validation.
    $effect_data = (new FormState())->setValues($form_state->getValue('data'));
    $this->zoneMember->validateConfigurationForm($form, $effect_data);
    // Update the original form values.
    $form_state->setValue('data', $effect_data->getValues());
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();
    // The zone member configuration is stored in the 'data' key in the form,
    // pass that through for submission.
    $member_data = (new FormState())->setValues($form_state->getValue('data'));
    $this->zoneMember->submitConfigurationForm($form, $member_data);
    // Update the original form values.
    $form_state->setValue('data', $effect_data->getValues());
    $this->zoneMember->setWeight($form_state->getValue('weight'));
    // If this is an add form, then the plugin will be fresh and uuid-less.
    // Add it to the zone's plugin collection.
    if (!$this->zoneMember->getUuid()) {
      $this->zone->addMember($this->zoneMember);
    }
    $this->zone->save();
    drupal_set_message($this->t('The zone member was successfully included in the zone.'));
    $form_state->setRedirectUrl($this->zone->urlInfo('edit-form'));
  }

}

