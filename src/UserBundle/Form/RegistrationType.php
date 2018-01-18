<?php

namespace UserBundle\Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;



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
->add('phone')
->add('is_newsletter')
            ->add('prenom')
            ->add('naissance', DateType::class, array(
    'widget' => 'single_text',
   'html5' => false,
    'attr' => ['class' => 'js-datepicker']))
            ->add('genre', ChoiceType::class, array(
            'choices' => array('monsieur' => 'Monsieur', 'madame' => 'Madame','mademoiselle' => 'Mademoiselle' )))
            ->add('adress', new UserAdressType(), array(
                'required' => false
            ))
            ->add('parrainEmail' );
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
