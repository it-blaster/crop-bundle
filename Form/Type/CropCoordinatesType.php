<?php

namespace Fenrizbes\CropBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CropCoordinatesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'crop_coordinates';
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
            ->setOptional(array(
                'validate',
                'instances'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['instances']) || !is_array($options['instances']) || !count($options['instances'])) {
            $options['instances'] = array(array());
        }

        for ($i = 0; $i < count($options['instances']); $i++) {
            $this->addChildForm($builder, $i, $this->prepareChildOptions($options, $i));
        }
    }

    /**
     * Prepares options for a child
     *
     * @param array $options
     * @param int $index
     * @return array
     */
    protected function prepareChildOptions(array $options, $index)
    {
        $child_options = array();

        if (isset($options['instances'][$index]) && is_array($options['instances'][$index])) {
            $child_options = $options['instances'][$index];
        }

        if (!isset($child_options['width'])) {
            $child_options['width'] = $options['width'];
        }

        if (!isset($child_options['height'])) {
            $child_options['height'] = $options['height'];
        }

        return $child_options;
    }

    /**
     * Adds a child form
     *
     * @param FormBuilderInterface $builder
     * @param int $index
     * @param array $options
     */
    protected function addChildForm(FormBuilderInterface $builder, $index, array $options)
    {
        $builder->add($index, 'crop_instance', array_merge(array(
            'label' => false
        ), $options));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['label'] = false;
    }
}