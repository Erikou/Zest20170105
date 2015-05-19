<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmployeeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',    'text')
            ->add('sesa',    'text')
            ->add('job', 'entity', array(
                'class'    => 'SEInputBundle:Job',
                'property' => 'description', 
                'multiple' => false,
                'expanded' => false
                ))
            ->add('status', 'entity', array(
                'class'    => 'SEInputBundle:Status',
                'property' => 'name', 
                'multiple' => false,
                'expanded' => false
                ))
            ->add('permanent', 'checkbox', array('required' => false))
            ->add('remarks', 'textarea', array('required' => false))
            ->add('save',      'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SE\InputBundle\Entity\Employee'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'se_inputbundle_employee';
    }
}