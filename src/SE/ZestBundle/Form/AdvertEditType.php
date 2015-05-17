<?php

namespace SE\ZestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdvertEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('date');
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'se_zestbundle_advert_edit';
    }

    public function getParent()
    {
      return new AdvertType();
    }
}
