<?php

namespace CommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ChooseLivraisonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('transportMethod', ChoiceType::class, array(
    'choices'  => array(
        'La Poste' =>  'La Poste',
        'Chronopost' => 'Chronopost',
        'Fedex' => 'Fedex',
        'DHL' => 'DHL',
        'Lille' => 'Lille',
        'Reims' => 'Reims'

    ),))
        ->add('save', SubmitType::class, array('label' => 'Confirmation')); //here is the problem
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
