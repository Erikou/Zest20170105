<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SE\InputBundle\Entity\EmployeeRepository;

class TransferType extends AbstractType
{
    protected $usrDepId;

    public function __construct(/*$usrDepId*/)
    {
        $this->usrDepId = 8;//$usrDepId;
    }
    
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
      	$usrDepId = $this->usrDepId;
      	
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

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'AppBundle\Entity\Transfer',
            	'usrDepId' => -1,
				'zzzconnard' => 'raaaah'
		));
	}

	public function getName()
	{
		return "TransferType";
	}
}