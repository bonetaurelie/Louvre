<?php

namespace AB\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

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
                'constraints'=>new Regex(array(
                    'pattern'=>'/\d/',
                    'match'=> false,
                    'message'=>'nom.message'
                ))
            ))
            ->add('prenom','text',array(
                'constraints'=>new Regex(array(
                    'pattern'=>'/\d/',
                    'match'=> false,
                    'message'=>'prenom.message'
                ))
            ))
            ->add('dateNaissance','date', array(
                'widget'=>'single_text',
                'required'=>false,
                'constraints'=>new NotBlank(array(
                    'message'=>'date.message'))
            ))
            ->add('pays','text',array(
                'constraints'=> new Regex(array(
                    'pattern'=>'/\d/',
                    'match'=> false,
                    'message'=>'pays.message'
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
