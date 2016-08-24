<?php

namespace CommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


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


    ),))
            ->add('montant')
            ->add('minimum_commande')
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
