<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('error_bubbling' => true))
            ->add('email', 'email', array('error_bubbling' => true))
            ->add('username', 'text', array('error_bubbling' => true))
            ->add('team', 'entity', array(
    				'class' => 'SEInputBundle:Team',	
    				'property' => 'name',
        			'multiple' => false,'expanded' => false,
            		'error_bubbling' => true
            	))
            ->add('abilitations', 'entity', array(
    				'class' => 'SEInputBundle:Abilitation',	
    				'property' => 'name',
        			'multiple' => true,'expanded' => true,
            		'error_bubbling' => true
            	));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return "AddUserType";
    }
}