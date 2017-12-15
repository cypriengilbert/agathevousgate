<?php

namespace CommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CommerceBundle\Entity\Product;
use CommerceBundle\Entity\Color;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;




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

      ->add('color1', EntityType::class , array(
   		'class' => 'CommerceBundle:Color',
           'choice_label' => 'name',
           'required' => false
		))
      ->add('color2', EntityType::class , array(
   		'class' => 'CommerceBundle:Color',
           'choice_label' => 'name',
           
           'required' => false


   	))
      ->add('color3', EntityType::class , array(
   		'class' => 'CommerceBundle:Color',
           'choice_label' => 'name',
           'required' => false


   	))
     
      ->add('product', EntityType::class , array(
   		'placeholder' => 'Choose an option',
   		'class' => 'CommerceBundle:Product',
           'choice_label' => 'name',
           'required' => false

   	))
      ->add('accessoire', EntityType::class , array(
   		'placeholder' => 'Choose an option',
   		'class' => 'CommerceBundle:Accessoire',
           'choice_label' => 'name',
           'required' => false,
   		'multiple' => false
   	))
    ->add('size', ChoiceType::class , array(
      'choices'  => array(
          'Mini' => 'Mini',
          'Standard' => 'Standard',
      ),
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
