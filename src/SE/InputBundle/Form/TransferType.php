<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SE\InputBundle\Entity\EmployeeRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TransferType extends AbstractType
{

	public function __construct(array $options = array())
	{
		/*$resolver = new OptionsResolver();
		$this->configureOptions($resolver);
	
		$this->options = $resolver->resolve($options);*/
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
      	$usrDepId = -1;
      	if (isset($options['max_length'])){
      		$usrDepId = $options['max_length'];
      	}
      	
		$builder
		->add('date_start', 'date', array('error_bubbling' => true))
		->add('employee', 'entity', array(
				'class' => 'SEInputBundle:Employee',
    			'query_builder' => function(EmployeeRepository $er) use ($usrDepId) {
        			return $er->getDepartementEmployees($usrDepId);
    			},
				'property' => 'nameDepartement',
				'error_bubbling' => true
		))
		->add('departement', 'entity', array(
				'class' => 'SEInputBundle:Departement',
				'property' => 'name',
				'error_bubbling' => true
		));
	}

	/*
	 * Confusing. OptionsResolverInterface is supposed to be deprecated and replaced with OptionsResolver
	 * but it creates a compilation error...
	 * Also using this function deactivates configureOptions, and data_class is undefined...
	 * I don't get this error by removing data_class from configureOptions...
	 * I'm confused.
	 * */
	public function getDefaultOptions(OptionsResolverInterface $resolver)
	{
		$this->configureOptions($resolver);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'AppBundle\Entity\Transfer',
            	'option1' => 14 //Not working ?
		));
	}

	public function getName()
	{
		return "TransferType";
	}
}