<?php

namespace AB\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BilletType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantite')
            ->add('type',ChoiceType::class,[
                    'choices' => [
                        'Choisissez votre type de billet' => [
                            'Demi-journée' => 'demi_journee',
                            'journee' =>'journee',
                        ],
                    ]
                ]
            )
            ->add('date', 'date')
            ->add('email')
            ->add('valider','submit')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AB\CoreBundle\Entity\Billet'
        ));
    }
}
