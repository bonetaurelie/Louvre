<?php

namespace AB\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Length;

class VisiteurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom','text',array(
                'constraints'=>new Length(array(
                    'min'=>2,
                    'minMessage'=>'nom.message'
                ))
            ))
            ->add('prenom','text',array(
                'constraints'=>new Length(array(
                    'min'=>2,
                    'minMessage'=>'prenom.message'
                ))
            ))
            ->add('dateNaissance','date', array(
                'widget'=>'single_text',
                'constraints'=>new Date(array(
                    'message'=>'date.message'))
            ))
            ->add('pays','text',array(
                'constraints'=> new Length(array(
                    'min'=>2,
                    'minMessage'=>'pays.message'
                ))
            ))
            ->add('tarifReduit','checkbox',array('required'=>false))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AB\CoreBundle\Entity\Visiteur'
        ));
    }
    
}
