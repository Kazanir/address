<?php

/**
 * @file
 * Contains \Drupal\address\Event\AvailableCountriesEvent.
 */

namespace Drupal\address\Event;

use Drupal\Core\Field\FieldDefinitionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the available countries event.
 *
 * @see \Drupal\address\Event\AddressEvents
 * @see \Drupal\address\Plugin\Field\FieldType\AddressItem::getAvailableCountries
 */
class AvailableCountriesEvent extends Event {

  /**
   * The available countries.
   *
   * A list of country codes.
   *
   * @var array
   */
  protected $availableCountries;

  /**
   * The field definition.
   *
   * @var \Drupal\Core\Field\FieldDefinitionInterface
   */
  protected $fieldDefinition;

  /**
   * Constructs a new AvailableCountriesEvent object.
   *
   * @param array $availableCountries
   *   The available countries.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $fieldDefinition
   *   The field definition.
   */
  public function __construct(array $availableCountries, FieldDefinitionInterface $fieldDefinition) {
    $this->availableCountries = $availableCountries;
    $this->fieldDefinition = $fieldDefinition;
  }

  /**
   * Gets the available countries.
   *
   * @return array
   *   The available countries.
   */
  public function getAvailableCountries() {
    return $this->availableCountries;
  }

  /**
   * Sets the available countries.
   *
   * @return $this
   */
  public function setAvailableCountries(array $availableCountries) {
    $this->availableCountries = $availableCountries;
    return $this;
  }

  /**
   * Gets the field definition.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface
   *   The field definition.
   */
  public function getFieldDefinition() {
    return $this->fieldDefinition;
  }

}

