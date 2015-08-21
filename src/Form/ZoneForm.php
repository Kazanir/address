<?php

/**
 * @file
 * Contains \Drupal\address\Form\ZoneForm.
 */

namespace Drupal\address\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityManager;
use Drupal\address\Entity\Zone;
use Drupal\Core\Form\FormStateInterface;
use Drupal\address\ZoneMemberManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ZoneForm extends EntityForm {

  /**
   * The zone storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $zoneStorage;

  /**
   * The zone member plugin manager.
   *
   * @var \Drupal\address\ZoneMemberManager
   */
  protected $zoneMemberManager;

  /**
   * Creates a ZoneForm instance.
   *
   * @param \Drupal\Core\Entity\EntityManager $entity_manager
   *   The entity manager to fetch storage from.
   *
   * @param \Drupal\address\ZoneMemberManager $zone_memberManager
   *   Our plugin manager for zone members.
   */
  public function __construct(EntityManager $entity_manager, ZoneMemberManager $zone_member_manager) {
    $this->zoneStorage = $entity_manager->getStorage('zone');
    $this->zoneMemberManager = $zone_member_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Entity\EntityManagerInterface $entity_manager */
    $entity_manager = $container->get('entity.manager');
    $plugin_manager = $container->get('plugin.manager.address.zonemember');
    return new static($entity_manager, $plugin_manager);
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $user_input = $form_state->getUserInput();
    $zone = $this->entity;

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $zone->getName(),
      '#maxlength' => 255,
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#title' => $this->t('Machine name'),
      '#default_value' => $zone->getId(),
      '#description' => $this->t('Only lowercase, underscore-separated letters allowed.'),
      '#machine_name' => [
        'pattern' => '[^a-z0-9_]+',
        'source' => ['name'],
      ],
      '#maxlength' => 255,
      '#required' => TRUE,
    ];
    $form['scope'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Scope'),
      '#default_value' => $zone->getScope(),
      '#maxlength' => 255,
    ];
    $form['priority'] = [
      '#type' => 'number',
      '#title' => $this->t('Priority'),
      '#default_value' => $zone->getPriority(),
    ];

    // Build the list of existing zone members for this zone.
    $form['members'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Zone Member'),
        $this->t('Weight'),
        $this->t('Operations'),
      ],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'zone-member-order-weight',
        ],
      ],
      '#attributes' => [
        'id' => 'zone-zone-members',
      ],
      '#empty' => t('There are currently no zone members in this zone. Add one by selecting an option below.'),
      // Render zone members below parent elements.
      '#weight' => 5,
    ];

    foreach ($this->entity->getMembers() as $key => $member) {
      $member_form =& $form['members'][$key];
      $member_form['#attributes']['class'][] = 'draggable';
      $member_form['#weight'] = isset($user_input['members']) ? $user_input['members'][$key]['weight'] : NULL;
      $member_form['member'] = [
        '#tree' => FALSE,
      ];

      // @todo: Add the rest of the member form here
      $member_form['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight for @title', ['@title' => $member->label()]),
        '#title_display' => 'invisible',
        '#default_value' => $member->getWeight(),
        '#attributes' => [
          'class' => ['zonemember-order-weight'],
        ],
      ];

      // We need the zone ID and the member key as arguments for the individual
      // forms.
      $margs = [
        'zone' => $this->entity->id(),
        'member' => $key,
      ];

      $links = [
        'edit' => [
          'title' => $this->t('Edit'),
          'url' => Url::fromRoute('address.zonemember.edit', $margs),
        ],
        'delete' => [
          'title' => $this->t('Delete'),
          'url' => Url::fromRoute('address.zonemember.delete', $margs),
        ],
      ];

      $member_form['operations'] = [
        '#type' => 'operations',
        '#links' => $links,
      ];
    }

    // Build the new image effect addition form and add it to the effect list.
    $new_member_options = [];
    $plugins = $this->zoneMemberManager->getDefinitions();
    uasort($plugins, function($a, $b) {
      return strcasecmp(@$a['type'], @$b['type']);
    });
    foreach ($plugins as $plugin => $definition) {
      $new_member_options[$plugin] = $definition['label'];
    }
    $form['members']['new'] = [
      '#tree' => FALSE,
      '#weight' => isset($user_input['weight']) ? $user_input['weight'] : NULL,
      '#attributes' => ['class' => ['draggable']],
    ];
    $form['members']['new']['member'] = [
      'new' => [
        '#type' => 'select',
        '#title' => $this->t('Zone member'),
        '#title_display' => 'invisible',
        '#options' => $new_member_options,
        '#empty_option' => $this->t('Select a member type'),
      ],
      [
        'add' => [
          '#type' => 'submit',
          '#value' => $this->t('Add'),
          '#validate' => ['::memberValidate'],
          '#submit' => ['::submitForm', '::memberSave'],
        ],
      ],
      '#prefix' => '<div class="zonemember-new">',
      '#suffix' => '</div>',
    ];

    $form['members']['new']['weight'] = [
      '#type' => 'weight',
      '#title' => $this->t('Weight for new member'),
      '#title_display' => 'invisible',
      '#default_value' => count($this->entity->getMembers()) + 1,
      '#attributes' => ['class' => ['zonemember-order-weight']],
    ];
    $form['members']['new']['operations'] = [
      'data' => [],
    ];




    return parent::form($form, $form_state);
  }

  /**
   * Validates the id field.
   */
  public function validateId(array $element, FormStateInterface $form_state, array $form) {
    $zone = $this->getEntity();
    $id = $element['#value'];
    if (!preg_match('/[a-z_]+/', $id)) {
      $form_state->setError($element, $this->t('The machine name must be in lowercase, underscore-separated letters only.'));
    }
    elseif ($zone->isNew()) {
      $loaded_zones = $this->zoneStorage->loadByProperties([
        'id' => $id,
      ]);
      if ($loaded_zones) {
        $form_state->setError($element, $this->t('The machine name is already in use.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $zone = $this->entity;

    try {
      $zone->save();
      drupal_set_message($this->t('Saved the %label zone.', [
        '%label' => $zone->label(),
      ]));
      $form_state->setRedirect('entity.zone.collection');
    }
    catch (\Exception $e) {
      drupal_set_message($this->t('The %label zone was not saved.', [
        '%label' => $zone->label()
      ]), 'error');
      $this->logger('address.zone')->error($e);
      $form_state->setRebuild();
    }
  }

  /**
   *
   */
  public function memberValidate($form, FormStateInterface $form_state) {
    if (!$form_state->getValue('new')) {
      $form_state->setErrorByName('new', $this->t('Select a zone member type to add.'));
    }
  }

  /**
   * Submit handler for adding a zone member.
   */
  public function memberSave($form, FormStateInterface $form_state) {
    $this->save($form, $form_state);
    // Check if this field has any configuration options.
    $member = $this->zoneMemberManager->createInstance($form_state->getValue('new'));
    // Load the configuration form for this option.
    $member_id = $this->entity->addMember($member);
    $this->entity->save();
    $form_state->setRedirect(
      'address.zone_member.edit_form',
      [
        'zone' => $this->entity->id(),
        'zone_member' => $member_id,
      ]
    );
  }
}

