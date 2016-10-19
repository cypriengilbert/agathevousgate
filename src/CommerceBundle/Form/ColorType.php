<?php

namespace CommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class ColorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('isbasic', CheckboxType::class)
            ->add('colorFile', 'vich_image', array(
              'required'      => false,
              'label' => false,

              'allow_delete'  => false, // not mandatory, default is true
              'download_link' => true, // not mandatory, default is true
            ))
            ->add('colorNoeud1File', 'vich_image', array(
              'required'      => false,
              'label' => false,

              'allow_delete'  => false, // not mandatory, default is true
              'download_link' => true, // not mandatory, default is true
            ))
            ->add('colorNoeud2File', 'vich_image', array(
              'required'      => false,
              'label' => false,

              'allow_delete'  => false, // not mandatory, default is true
              'download_link' => true, // not mandatory, default is true
            ))
            ->add('colorNoeud3File', 'vich_image', array(
              'required'      => false,
              'label' => false,
              'allow_delete'  => false, // not mandatory, default is true
              'download_link' => true, // not mandatory, default is true
            ))
            ->add('tissuColorFile', 'vich_image', array(
              'required'      => false,
              'label' => false,
              'allow_delete'  => false, // not mandatory, default is true
              'download_link' => true, // not mandatory, default is true
            ))
            ->add('tissuMilieuColorFile', 'vich_image', array(
              'required'      => false,
              'label' => false,
              'allow_delete'  => false, // not mandatory, default is true
              'download_link' => true, // not mandatory, default is true
            ))
            ->add('couleurPochette', 'vich_image', array(
              'required'      => false,
              'label' => false,
              'allow_delete'  => false, // not mandatory, default is true
              'download_link' => true, // not mandatory, default is true
            ))
            ->add('couleurBoutons', 'vich_image', array(
              'required'      => false,
              'label' => false,
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
            'data_class' => 'CommerceBundle\Entity\Color'
        ));
    }
}
