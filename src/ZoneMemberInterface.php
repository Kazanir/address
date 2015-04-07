<?php

/**
 * @file
 * Contains \Drupal\address\ZoneMemberInterface.
 */

namespace Drupal\address;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use CommerceGuys\Zone\Model\ZoneMemberInterface as ExternalZoneMemberInterface;

/**
 * Defines the interface for zone members.
 */
interface ZoneMemberInterface extends PluginInspectionInterface, ExternalZoneMemberInterface {}

