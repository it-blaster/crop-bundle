<?php

namespace Fenrizbes\CropBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CropInstanceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'crop_instance';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array(
                'width',
                'height'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('left', 'hidden', array(
                'attr' => array(
                    'class' => 'fenrizbes-crop-bundle-instance-left'
                )
            ))
            ->add('top', 'hidden', array(
                'attr' => array(
                    'class' => 'fenrizbes-crop-bundle-instance-top'
                )
            ))
            ->add('width', 'hidden', array(
                'attr' => array(
                    'class' => 'fenrizbes-crop-bundle-instance-width'
                )
            ))
            ->add('height', 'hidden', array(
                'attr' => array(
                    'class' => 'fenrizbes-crop-bundle-instance-height'
                )
            ))
            ->add('min_width', 'hidden')
            ->add('min_height', 'hidden')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data = $form->getData();

        if (empty($data)) {
            $form->setData(array(
                'left'       => 0,
                'top'        => 0,
                'width'      => $options['width'],
                'height'     => $options['height'],
                'min_width'  => $options['width'],
                'min_height' => $options['height']
            ));
        }
    }
}