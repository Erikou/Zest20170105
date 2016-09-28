<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newPassword', 'repeated', array(
                    'type' => 'password',
            		'invalid_message' => 'The password fields must match.',
            		'required' => true,
                    'first_options'  => array('label' => 'New-Password'),
                    'second_options' => array('label' => 'Repeat'),
                ))
            ->add('save', 'submit');
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