<?php

namespace CommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use CommerceBundle\Entity\ModeLivraison;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

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

          ->add('transportMethod', EntityType::class , array(
       		'class' => 'CommerceBundle:ModeLivraison',
       		'choice_label' => 'description',
          'query_builder' => function (EntityRepository $er) {
                  return $er->createQueryBuilder('u')
                      ->orderBy('u.id', 'ASC')
                       ->where('u.price > 1.8');
              }
       	));}


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
