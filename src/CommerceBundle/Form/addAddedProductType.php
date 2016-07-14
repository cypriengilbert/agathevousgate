<?php

namespace CommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CommerceBundle\Entity\Product;
use CommerceBundle\Entity\Color;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class addAddedProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity')
            ->add('color1', EntityType::class, array(
            'class' => 'CommerceBundle:Color',
            'choice_label' => 'name',
              ))
              ->add('color2', EntityType::class, array(
              'class' => 'CommerceBundle:Color',
              'choice_label' => 'name',
                ))
                ->add('color3', EntityType::class, array(
                'class' => 'CommerceBundle:Color',
                'choice_label' => 'name',
                  ))
                  ->add('color4', EntityType::class, array(
                  'class' => 'CommerceBundle:Color',
                  'choice_label' => 'name',
                    ))
                    ->add('color5', EntityType::class, array(
                    'class' => 'CommerceBundle:Color',
                    'choice_label' => 'name',
                      ))
                      ->add('color6', EntityType::class, array(
                      'class' => 'CommerceBundle:Color',
                      'choice_label' => 'name',
                        ))
                        ->add('color7', EntityType::class, array(
                        'class' => 'CommerceBundle:Color',
                        'choice_label' => 'name',
                          ))
                          ->add('color8', EntityType::class, array(
                          'class' => 'CommerceBundle:Color',
                          'choice_label' => 'name',
                            ))
                            ->add('color9', EntityType::class, array(
                            'class' => 'CommerceBundle:Color',
                            'choice_label' => 'name',
                              ))
                              ->add('color10', EntityType::class, array(
                              'class' => 'CommerceBundle:Color',
                              'choice_label' => 'name',
                                ))
            ->add('product', EntityType::class, array(
'placeholder' => 'Choose an option',
            'class' => 'CommerceBundle:Product',
            'choice_label' => 'name',


))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CommerceBundle\Entity\AddedProduct'
        ));
    }
}