<?php

namespace CommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('transportMethod', ChoiceType::class, array(
    'choices'  => array(
        'La Poste' =>  'La Poste',
        'Chronopost' => 'Chronopost',
        'Fedex' => 'Fedex',
        'DHL' => 'DHL',
        'Sur place' => 'Sur place'
    ),))
            ->add('client')
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