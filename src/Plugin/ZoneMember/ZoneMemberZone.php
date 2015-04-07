<?php

namespace Drupal\address\Plugin\ZoneMember;

use CommerceGuys\Zone\Model as Lib;
use CommerceGuys\Addressing\Model\AddressInterface;
use CommerceGuys\Addressing\Model\SubdivisionInterface;
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
class ZoneMemberZone extends Lib\ZoneMember implements ZoneMemberInterface {

  public function buildConfigForm(array $form, FormStateInterface $form_state) {
    $form['zone'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'zone',
      '#tags' => FALSE,
    ];

    return $form;
  }

}

