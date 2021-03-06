<?php

namespace BlogBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class BlogType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title')
            ->add('username', HiddenType::class)
            ->add('body')
            ->add('category',
                EntityType::class,array(
                    'class'=>'CategoryBundle:Category',
                    'choice_label'=>'name'
                ))

            ->add('optionalPhoto', FileType::class,
                array('label' => 'Photo (Optional)', 'data_class' => null))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'compound' => true,
            'data_class' => 'BlogBundle\Entity\Blog'
        ));
    }
}
