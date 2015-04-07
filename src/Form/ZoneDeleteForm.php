<?php

/**
 * @file
 * Contains \Drupal\address\Zone\Form\ZoneDeleteForm.
 */

namespace Drupal\address\Zone\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds the form to delete a zone.
 */
class ZoneDeleteForm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      $this->entity->delete();
      $form_state->setRedirectUrl($this->getCancelUrl());
      drupal_set_message($this->t('Zone %label has been deleted.', array('%label' => $this->entity->label())));
    }
    catch (\Exception $e) {
      // Catching exceptions thrown when some zone member somewhere depends on
      // this zone.
      drupal_set_message($this->t('Zone %label could not be deleted.', array('%label' => $this->entity->label())), 'error');
      $this->logger('address')->error($e);
    }
  }
}

