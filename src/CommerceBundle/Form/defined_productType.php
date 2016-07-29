<?php

namespace CommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CommerceBundle\Entity\Product;
use CommerceBundle\Entity\Color;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class defined_productType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isactive')
            ->add('image', 'vich_image', array(
              'required'      => false,
              'allow_delete'  => false, // not mandatory, default is true
              'download_link' => true, // not mandatory, default is true
            ))
            ->add('color1', EntityType::class , array(
         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',


         	))
            ->add('color2', EntityType::class , array(
         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',


         	))
            ->add('color3', EntityType::class , array(
         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',


         	))
            ->add('color4', EntityType::class , array(
         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',
            'required'    => false,

         	))
            ->add('color5', EntityType::class , array(
         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',
            'required'    => false,

         	))
            ->add('color6', EntityType::class , array(
         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',
            'required'    => false,

         	))
            ->add('color7', EntityType::class , array(
         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',
            'required'    => false,

         	))
            ->add('color8', EntityType::class , array(
         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',
            'required'    => false,

         	))
          ->add('color9', EntityType::class , array(
         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',
            'required'    => false,

         	))
            ->add('color10', EntityType::class , array(
         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',
            'required'    => false,

         	))
          ->add('product', EntityType::class , array(
          'placeholder' => 'Choose an option',
          'class' => 'CommerceBundle:Product',
          'choice_label' => 'name',

        ))
        ->add('collection', EntityType::class , array(
        'placeholder' => 'Choose an option',
        'class' => 'CommerceBundle:Collection',
        'choice_label' => 'title',

        ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CommerceBundle\Entity\defined_product'
        ));
    }
}
