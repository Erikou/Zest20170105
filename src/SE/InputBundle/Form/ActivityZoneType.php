<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActivityZoneType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('zone', 'entity', array(
                    'class'    => 'SEInputBundle:Zone',
                    'property' => 'name', 
                    'multiple' => false,
                    'expanded' => false
                    ), array('required' => false))
            ->add('target', 'number', array('required' => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SE\InputBundle\Entity\ActivityZone'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'se_inputbundle_activityzone';
    }
}
