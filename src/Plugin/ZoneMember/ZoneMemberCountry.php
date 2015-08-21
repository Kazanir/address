<?php

namespace Drupal\address\Plugin\ZoneMember;

use CommerceGuys\Addressing\Model\AddressInterface;
use CommerceGuys\Addressing\Model\SubdivisionInterface;
use Drupal\address\ZoneMemberBase;
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
class ZoneMemberCountry extends ZoneMemberBase implements ZoneMemberInterface {

  public function buildConfigForm(array $form, FormStateInterface $form_state) {

  }

  public function defaultConfiguration() {
    return [
      'address' => [], // @todo: Whatever ends up here.
    ];
  }

  public function match(AddressInterface $address) {

  }

}

