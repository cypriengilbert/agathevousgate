<?php

namespace CommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CommerceBundle\Entity\Product;
use CommerceBundle\Entity\Color;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


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
            ->add('name')

            ->add('description', TextareaType::class)

            ->add('image', 'vich_image', array(
              'required'      => false,
              'allow_delete'  => false, // not mandatory, default is true
              'download_link' => true, // not mandatory, default is true
            ))
            ->add('color1', EntityType::class , array(
              'placeholder' => 'Choose a color',
              'required'    => false,

         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',


         	))
            ->add('color2', EntityType::class , array(
              'placeholder' => 'Choose a color',
              'required'    => false,

         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',


         	))
            ->add('color3', EntityType::class , array(
              'placeholder' => 'Choose a color',
              'required'    => false,

         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',


         	))
            ->add('color4', EntityType::class , array(
              'placeholder' => 'Choose a color',

         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',
            'required'    => false,

         	))
            ->add('color5', EntityType::class , array(
              'placeholder' => 'Choose a color',

         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',
            'required'    => false,

         	))
            ->add('color6', EntityType::class , array(
              'placeholder' => 'Choose a color',

         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',
            'required'    => false,

         	))
            ->add('color7', EntityType::class , array(
              'placeholder' => 'Choose a color',

         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',
            'required'    => false,

         	))
            ->add('color8', EntityType::class , array(
              'placeholder' => 'Choose a color',

         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',
            'required'    => false,

         	))
          ->add('color9', EntityType::class , array(
            'placeholder' => 'Choose a color',

         		'class' => 'CommerceBundle:Color',
         		'choice_label' => 'name',
            'required'    => false,

         	))
            ->add('color10', EntityType::class , array(
              'placeholder' => 'Choose a color',
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
        ->add('discount')
        ->add('enfants', EntityType::class, array(
                            'class' => 'CommerceBundle:defined_product',
                            'multiple' => true,
                                'expanded' => true,
                                'choice_label' => 'id',
                                 'required' => true,
                                 'query_builder' => function (EntityRepository $er) {
                                         return $er->createQueryBuilder('u')
                                             ->orderBy('u.id', 'ASC')
                                              ->where('u.product = 10 or u.product = 11 or u.product = 4 or u.product = 12');
                                     },
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
