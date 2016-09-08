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
            ->add('defaultTeam', 'entity', array(
                'class'    => 'SEInputBundle:Team',
                'property' => 'name', 
                'multiple' => false,
                'expanded' => false
                ), array('required' => false))
            ->add('defaultShift', 'entity', array(
                'class'    => 'SEInputBundle:Shift',
                'property' => 'identifier', 
                'multiple' => false,
                'expanded' => false
                ), array('required' => false))
            ->add('defaultActivity', 'entity', array(
                'class'    => 'SEInputBundle:Activity',
                'property' => 'name', 
                'multiple' => false,
                'expanded' => false
                ), array('required' => false))
            ->add('defaultZone', 'entity', array(
                'class'    => 'SEInputBundle:Zone',
                'property' => 'name',
                'multiple' => false,
                'expanded' => false
                ), array('required' => false))
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
            ->add('startDate', 'date', array(
                'widget' => 'choice',
                'label' => "Job start date",
                'years' => range(1990,3000),
                'format' => 'ddMMMyyyy',
                'required' => true)
            )
            ->add('endDate', 'date', array(
                'widget' => 'choice',
                'label' => "Job end date",
                'format' => 'ddMMMyyyy',
                'years' => range(1990,2000),
                'required' => true)
            )
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
