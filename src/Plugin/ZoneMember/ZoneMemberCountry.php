<?php

namespace Drupal\address\Plugin\ZoneMember;

use CommerceGuys\Zone\Model as Lib;
use CommerceGuys\Addressing\Model\AddressInterface;
use CommerceGuys\Addressing\Model\SubdivisionInterface;
use Drupal\address\ZoneMemberInterface;

/**
 * Matches a country, its subdivisions, and its postal codes.
 *
 * @ZoneMember(
 *   id = "zone_member_country",
 *   label = @Translation("Zone Member: Country"),
 *   description = @Translation("Matches a country, its subdivisions, and its
 *   postal codes.")
 * )
 */
class ZoneMemberCountry extends Lib\ZoneMember implements ZoneMemberInterface {

  public function buildConfigForm(array $form, FormStateInterface $form_state) {

  }

}

