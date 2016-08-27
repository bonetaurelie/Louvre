<?php

namespace AB\CoreBundle\Form;

use ClassesWithParents\D;
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
                'required'=>false
            ))
            ->add('prenom','text',array(
                'required'=>false
            ))
            ->add('dateNaissance','date', array(
                'required'=>false,
                'widget'=>'single_text','input' => 'datetime', 'format' => 'dd/MM/y',
            ))
            ->add('pays','text',array(
                'required'=>false
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
