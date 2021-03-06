<?php

/**
 * @file
 * Contains \Drupal\address\Plugin\Validation\Constraint\CountryConstraintValidator.
 */

namespace Drupal\address\Plugin\Validation\Constraint;

use CommerceGuys\Addressing\Repository\CountryRepositoryInterface;
use Drupal\address\AddressInteface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates the country constraint.
 */
class CountryConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * The country repository.
   *
   * @var \CommerceGuys\Addressing\Repository\CountryRepositoryInterface
   */
  protected $countryRepository;

  /**
   * Constructs a new CountryConstraintValidator object.
   *
   * @param \CommerceGuys\Addressing\Repository\CountryRepositoryInterface $countryRepository
   *   The country repository.
   */
  public function __construct(CountryRepositoryInterface $countryRepository) {
    $this->countryRepository = $countryRepository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('address.country_repository'));
  }

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    if (!($value instanceof AddressInterface)) {
      throw new UnexpectedTypeException($value, 'AddressInterface');
    }

    $address = $value;
    $countryCode = $address->getCountryCode();
    if ($countryCode === NULL || $countryCode === '') {
      return;
    }

    $countries = $this->countryRepository->getList();
    if (!isset($countries[$countryCode])) {
      $this->context->buildViolation($constraint->message)
        ->setParameter('%value', $this->formatValue($countryCode))
        ->addViolation();
    }
  }

}
