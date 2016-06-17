<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAbilitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('description', 'text')
            ->add('permissions', 'entity', array(
    				'class' => 'SEInputBundle:Permission',	
    				'property' => 'description',
        			'multiple' => true,'expanded' => true,
            	));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Abilitation',
        ));
    }

    public function getName()
    {
        return "EditAbilitationType";
    }
}