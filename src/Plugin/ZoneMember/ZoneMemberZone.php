<?php

namespace Drupal\address\Plugin\ZoneMember;

use CommerceGuys\Addressing\Model\AddressInterface;
use CommerceGuys\Addressing\Model\SubdivisionInterface;
use Drupal\address\ZoneMemberBase;
use Drupal\address\ZoneMemberInterface;

/**
 * Matches a single zone.
 *
 * @ZoneMember(
 *   id = "zone_member_zone",
 *   label = @Translation("Zone Member: Zone"),
 *   description = @Translation("Matches a single zone.")
 * )
 */
class ZoneMemberZone extends ZoneMemberBase implements ZoneMemberInterface {

  public function buildConfigForm(array $form, FormStateInterface $form_state) {
    $form['zone'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'zone',
      '#tags' => FALSE,
    ];

    return $form;
  }

  public function defaultConfiguration() {
    return [
      'zone_id' => '',
    ];
  }

  public function match(AddressInterface $address) {

  }

}

