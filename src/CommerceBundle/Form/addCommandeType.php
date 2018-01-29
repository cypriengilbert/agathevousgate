<?php

namespace CommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class addCommandeType extends AbstractType
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
             ->add('client', EntityType::class , array(
                'required'   => true,
                
                'class' => 'UserBundle:User',
                'choice_label'=> function ($user) {
                    $full_name = $user->getPrenom().' '.$user->getNom();
                    return $full_name;
                }
                
                ))
                
            ->add('date', 'date')
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
