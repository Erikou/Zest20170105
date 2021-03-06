<?php
/**
 * Created by IntelliJ IDEA.
 * User: louis
 * Date: 24/05/2016
 * Time: 10:41
 */

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SE\InputBundle\Entity\TeamRepository;

class AddUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('email', 'email')
            ->add('username', 'text')
            ->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                ))
            ->add('team', 'entity', array(
    				'class' => 'SEInputBundle:Team',	
    				'property' => 'name',
        			'multiple' => false,'expanded' => false,
            		'error_bubbling' => true,
            		'query_builder' => function(TeamRepository $tr) {
            			return $tr->getCurrentTeamsQueryBuilder();
            		},
            	))
            ->add('roles', 'entity', array(
    				'class' => 'SEInputBundle:Abilitation',	
    				'property' => 'name',
        			'multiple' => true,'expanded' => true,
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