<?php
/**
 * @file
 * Contains \Drupal\address\Entity\Zone.
 */

namespace Drupal\address\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use CommerceGuys\Zone\Model\ZoneInterface;
use CommerceGuys\Zone\Model\ZoneMemberInterface;
use CommerceGuys\Addressing\Model\AddressInterface;
use Drupal\Core\Plugin\DefaultLazyPluginCollection;
use CommerceGuys\Zone\Exception\UnexpectedTypeException;

/**
 * Defines the Zone configuration entity.
 *
 * @ConfigEntityType(
 *   id = "zone",
 *   label = @Translation("Zone"),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\address\Form\ZoneForm",
 *       "edit" = "Drupal\address\Form\ZoneForm",
 *       "delete" = "Drupal\address\Form\ZoneDeleteForm"
 *     },
 *     "list_builder" = "Drupal\address\Controller\ZoneListBuilder"
 *   },
 *   admin_permission = "administer zones",
 *   config_prefix = "zone",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "scope" = "scope",
 *     "priority" = "priority",
 *     "members" = "members"
 *   },
 *   links = {
 *     "collection" = "/admin/config/regional/zones",
 *     "edit-form" = "/admin/config/regional/zones/manage/{zone}",
 *     "delete-form" = "/admin/config/regional/zones/manage/{zone}/delete"
 *   }
 * )
 */

class Zone extends ConfigEntityBase implements ZoneInterface {

  /**
   * Zone id.
   *
   * @var string
   */
  protected $id;

  /**
   * Zone name.
   *
   * @var string
   */
  protected $name;

  /**
   * Zone scope.
   *
   * @var string
   */
  protected $scope;

  /**
   * Zone priority.
   *
   * @var int
   */
  protected $priority;

  /**
   * Zone members.
   *
   * @var array
   */
  protected $members = [];

  /**
   * Zone members collection.
   *
   * @var \Drupal\Core\Plugin\DefaultLazyPluginCollection
   */
  protected $membersCollection;

  /**
   * Returns the string representation of the zone.
   *
   * @return string
   */
  public function __toString() {
    return $this->name;
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function setId($id) {
    $this->id = $id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->name;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getScope() {
    return $this->scope;
  }

  /**
   * {@inheritdoc}
   */
  public function setScope($scope) {
    $this->scope = $scope;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPriority() {
    return $this->priority;
  }

  /**
   * {@inheritdoc}
   */
  public function setPriority($priority) {
    $this->priority = $priority;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMembers() {
    if (!$this->membersCollection) {
      if (!is_array($this->members)) {
        $this->members = [];
      }
      $this->membersCollection = new DefaultLazyPluginCollection(
        $this->getZoneMemberPluginManager(),
        $this->members
      );
      $this->membersCollection->sort();
    }

    return $this->membersCollection;
  }

  /**
   * Returns the zone member plugin manager.
   *
   * @return \Drupal\Component\Plugin\PluginManagerInterface
   *   The zone member plugin manager.
   */
  protected function getZoneMemberPluginManager() {
    return \Drupal::service('plugin.manager.address.zonemember');
  }

  /**
   * {@inheritdoc}
   */
  public function setMembers($members) {
    if (!($members instanceof DefaultLazyPluginCollection)) {
      throw new UnexpectedTypeException($members, 'DefaultLazyPluginException');
    }
    $this->membersCollection = $members;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function hasMembers() {
    return $this->membersCollection->count() !== 0;
  }

  /**
   * {@inheritdoc}
   */
  public function addMember(ZoneMemberInterface $member) {
    $member->setId($this->uuidGenerator()->generate());
    $this->membersCollection->set($member->getId(), $member);

    return $member->getId();
  }

  /**
   * {@inheritdoc}
   */
  public function removeMember(ZoneMemberInterface $member) {
    if ($this->hasMember($member)) {
      $this->membersCollection->remove($member);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function hasMember(ZoneMemberInterface $member) {
    return $this->membersCollection->has($member);
  }

  /**
   * {@inheritdoc}
   */
  public function match(AddressInterface $address) {
    foreach ($this->membersCollection as $member) {
      if ($member->match($address)) {
        return TRUE;
      }
    }
    return FALSE;
  }

}

