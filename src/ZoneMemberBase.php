<?php

namespace Drupal\address;

use CommerceGuys\Zone\Model\ZoneMember as ExternalZoneMember;
use Drupal\address\ZoneMemberInterface;

abstract class ZoneMemberBase extends ExternalZoneMember {

  /**
   * The image effect ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The plugin ID.
   *
   * @var string
   */
  protected $pluginId;

  /**
   * Gets the plugin ID.
   *
   * @return string
   */
  public function getPluginId() {
    return $this->pluginId;
  }

  /**
   * Get the plugin definition.
   *
   * @return array
   */
  public function getPluginDefinition() {
    return $this->pluginDefinition;
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $pluginId, $pluginDefinition) {
    $this->configuration = $configuration;
    $this->pluginId = $pluginId;
    $this->pluginDefinition = $pluginDefinition;
    $this->setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition) {
    return new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $container->get('logger.factory')->get('image')
    );
  }

  public function getConfiguration() {
    return [
      'id' => $this->getId(),
      'plugin_id' => $this->pluginId,
      'data' => $this->configuration
    ];
  }

  public function setConfiguration($configuration) {
    $configuration += [
      'data' => [],
      'id' => '',
    ];

    $this->configuration = $configuration['data'] + $this->defaultConfiguration();
    $this->setId($configuration['id']);

    return $this;
  }

  abstract public function defaultConfiguration();

}

