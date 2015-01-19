<?php

namespace SS6\ShopBundle\Form\Admin\Customer;

use SS6\ShopBundle\Model\Customer\UserData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class UserFormType extends AbstractType {

	/**
	 * @var string
	 */
	private $scenario;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Config\DomainConfig[]
	 */
	private $domains;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	private $pricingGroups;

	/**
	 * @param string $scenario
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig[] $domains
	 * @param \SS6\ShopBundle\Model\Domain\SelectedDomain $selectedDomain
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]|null $pricingGroups
	 */
	public function __construct($scenario, $domains = null, $selectedDomain = null, $pricingGroups = null) {
		$this->scenario = $scenario;
		$this->domains = $domains;
		$this->selectedDomain = $selectedDomain;
		$this->pricingGroups = $pricingGroups;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'user';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('firstName', 'text', [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím jméno']),
				],
			])
			->add('lastName', 'text', [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím příjmení']),
				],
			])
			->add('email', 'email', [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím e-mail']),
					new Constraints\Email(['message' => 'Vyplňte prosím platný e-mail']),
				],
			])
			->add('password', 'repeated', [
				'type' => 'password',
				'required' => $this->scenario === CustomerFormType::SCENARIO_CREATE,
				'first_options' => [
					'constraints' => [
						new Constraints\NotBlank([
							'message' => 'Vyplňte prosím heslo',
							'groups' => ['create'],
						]),
						new Constraints\Length(['min' => 5, 'minMessage' => 'Heslo musí mít minimálně {{ limit }} znaků']),
					],
				],
				'invalid_message' => 'Hesla se neshodují',
			]);

		if ($this->scenario === CustomerFormType::SCENARIO_CREATE) {
			$domainsNamesById = [];
			foreach ($this->domains as $domain) {
				$domainsNamesById[$domain->getId()] = $domain->getDomain();
			}

			$builder
				->add('domainId', 'choice', [
					'required' => true,
					'choices' => $domainsNamesById,
					'data' => $this->selectedDomain->getId(),
				]);
		}

		$builder
			->add('pricingGroup', 'choice', [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->pricingGroups, 'name', [], 'domainId', 'id'),
			]);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => UserData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
