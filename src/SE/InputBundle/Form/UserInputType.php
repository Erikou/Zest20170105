<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class UserInputType extends AbstractType
{

    /**
     * @var \DateTime
     */
    private $date;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'entity', array(
                'class'    => 'SEInputBundle:User',
                'property' => 'name', 
                'multiple' => false,
                'expanded' => false,
                'query_builder' => function(EntityRepository $er) {
                  return $er->createQueryBuilder('u')
                            ->select('u')
                            ->leftJoin('u.abilitations', 'ab')
                            ->addSelect('ab')
                            ->leftJoin('ab.permissions', 'p')
                            ->addSelect('p')
                            ->where("p.name = 'ROLE_INPUT_EDIT'")
                            ->orderBy("u.name", "ASC");
                  }
                ), array('required' => true))
            ->add('team', 'entity', array(
                'class'    => 'SEInputBundle:Team',
                'property' => 'name', 
                'multiple' => false,
                'expanded' => false,
                'query_builder' => function(EntityRepository $er) {
                  return $er->createQueryBuilder('u')
                            ->select('u')
                            ->where("u.statusControl = 1")
                            ->orderBy("u.masterId", "ASC");
                  }
                ), array('required' => true))
            ->add('shift', 'entity', array(
                'class'    => 'SEInputBundle:Shift',
                'property' => 'identifier', 
                'multiple' => false,
                'expanded' => true
                ), array('required' => true))
            ->add('dateInput', 'date', array(
                  'widget' => 'single_text',
                  'data' => $this->date
                ))
            ->add('otStartTime', 'time', array(
                  'widget' => 'single_text',
                  'required' => true))
            ->add('otEndTime', 'time', array(
                  'widget' => 'single_text',
                  'required' => true))
            ->add('input_entries', 'collection', array(
                'type'         => new InputEntryType(),
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'prototype_name' => 'entry_name',
                ))
            ->add('save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SE\InputBundle\Entity\UserInput'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'se_inputbundle_userinput';
    }
}
