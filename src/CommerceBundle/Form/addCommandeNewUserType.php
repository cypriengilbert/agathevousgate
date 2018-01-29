<?php

namespace CommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use UserBundle\Form\RegistrationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class addCommandeNewUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('paiementMethod', ChoiceType::class, array(
    'choices'  => array(
        'Paypal' =>  'Paypal',
        'Liquide' => 'Liquide',
        'Chèque' => 'Chèque',
        'Carte bancaire' => 'Carte bancaire'
    ),))
    ->add('transportMethod', EntityType::class , array(
        'placeholder' => 'Livraison ?',
        'required'   => true,
        
        'class' => 'CommerceBundle:ModeLivraison',
        'choice_label' => 'description',


    ))
    ->add('client', new RegistrationType())

             
            ->add('date', 'date',array(
                'data' => new \DateTime("now")))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CommerceBundle\Entity\Commande'
        ));
    }
}
