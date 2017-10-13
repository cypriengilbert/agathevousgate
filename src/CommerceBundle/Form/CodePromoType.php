<?php

namespace CommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use CommerceBundle\Entity\Product;
use CommerceBundle\Entity\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class CodePromoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('dateDebut', 'date')
            ->add('dateFin', 'date')
            ->add('genre',ChoiceType::class, array(
    'choices'  => array(
        'pourcentage' =>  'Pourcentage',
        'remise' => 'Remise en €',
        'fdp' => 'Frais de port',
        'fdp-remise' => 'Remise + Frais de port',



    ),))
            ->add('montant')
            ->add('minimum_commande')
            ->add('isAutomatic')
            ->add('productAuto1', EntityType::class, array(
                'class' => 'CommerceBundle:Product',
                'choice_label' => 'name',
               'multiple' => false,
               'expanded' => false,
                'required' => false,
         ))
         ->add('productAuto2', EntityType::class, array(
            'class' => 'CommerceBundle:Product',
            'choice_label' => 'name',
           'multiple' => false,
           'expanded' => false,
            'required' => false,
     ))

     ->add('collectionAuto1', EntityType::class, array(
        'class' => 'CommerceBundle:Collection',
        'choice_label' => 'title',
       'multiple' => false,
       'expanded' => false,
        'required' => false,
 ))
 ->add('collectionAuto2', EntityType::class, array(
    'class' => 'CommerceBundle:Collection',
    'choice_label' => 'title',

   'multiple' => false,
   'expanded' => false,

    'required' => false,
))


            ->add('quantityMin1')
            ->add('quantityMin2')
            

        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CommerceBundle\Entity\CodePromo'
        ));
    }
}
