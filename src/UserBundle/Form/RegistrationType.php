<?php

namespace UserBundle\Form;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;


class RegistrationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('nom')
            ->add('prenom')
            ->add('adress', new UserAdressType())
            ->add('parrain' )
        ;
    }

    public function getParent()
        {
            return 'FOS\UserBundle\Form\Type\RegistrationFormType';


        }

        public function getBlockPrefix()
        {
            return 'app_user_registration';
        }


        public function getName()
        {
            return $this->getBlockPrefix();
        }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User'
        ));
    }
}
