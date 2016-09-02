<?php

namespace CommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
/*use Symfony\Component\Form\Extension\Core\Type\CollectionType;*/
use CommerceBundle\Form\ColorType;
use CommerceBundle\Entity\Color;
use Doctrine\ORM\EntityRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;




class CollectionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')



          ->add('colors', EntityType::class, array(
                                   'class' => 'CommerceBundle:Color',
                                   'choice_label' => 'name',
                                  'multiple' => true,
                                  'expanded' => true,
                                   'required' => true,
                                   'query_builder' => function (EntityRepository $er) {
                                           return $er->createQueryBuilder('u')
                                               ->orderBy('u.id', 'ASC')
                                                ->where('u.isActive = true');
                                       }
                               ))
            ->add('description')
            ->add('imageCollection', 'vich_image', array(
              'required'      => false,
              'allow_delete'  => false, // not mandatory, default is true
              'download_link' => true, // not mandatory, default is true
            ))
            ->add('imageCollectionCarre', 'vich_image', array(
              'required'      => false,
              'allow_delete'  => false, // not mandatory, default is true
              'download_link' => true, // not mandatory, default is true
            ))
            ->add('imageCollectionIcone', 'vich_image', array(
              'required'      => false,
              'allow_delete'  => false, // not mandatory, default is true
              'download_link' => true, // not mandatory, default is true
            ))



        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CommerceBundle\Entity\Collection'
        ));
    }
}
