<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('date_start', 'date', array('error_bubbling' => true))
		->add('employee', 'entity', array(
				'class' => 'SEInputBundle:Employee',
				'property' => 'nameDepartement',
				'error_bubbling' => true
		))
		->add('departement', 'entity', array(
				'class' => 'SEInputBundle:Departement',
				'property' => 'name',
				'error_bubbling' => true
		));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'AppBundle\Entity\Transfer',
		));
	}

	public function getName()
	{
		return "TransferType";
	}
}