<?php

namespace AB\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class BilletType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantite','integer',array(
                'required'=>false
            ))
            ->add('type',ChoiceType::class,[
                    'choices' => [
                        'choix.type' => [
                            'demi_journee' => 'demi.type',
                            'journee' =>'type.resa',
                        ],
                    ]
                ]
            )
            ->add('date', 'date', array(
                'widget'=>'single_text','input' => 'datetime', 'format' => 'dd/MM/y',
                ))
            ->add('email','email',array(
                'required'=>false
            ))
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
