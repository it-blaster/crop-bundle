<?php

namespace Fenrizbes\CropBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Image;

class CroppableType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'croppable';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setOptional(array(
                'width',
                'height',
                'instances',
                'validate',
                'max_canvas_width',
                'max_canvas_height'
            ))
            ->setDefaults(array(
                'required'          => false,
                'validate'          => true,
                'max_canvas_width'  => 640,
                'max_canvas_height' => 480
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        unset($options['max_canvas_width']);
        unset($options['max_canvas_height']);

        $builder
            ->add('image', 'uploadable', array(
                'label'            => $options['label'],
                'file_constraints' => $this->getConstraints($options)
            ))
            ->add('coordinates', 'crop_coordinates', $options)
        ;
    }

    /**
     * Creates and returns constraints for a file field
     *
     * @param $options
     * @return array
     */
    public function getConstraints($options)
    {
        $constraints = array();

        if ($options['validate']) {
            $constraints[] = new Image(array(
                'mimeTypes' => array(
                    'image/gif',
                    'image/png',
                    'image/jpg',
                    'image/jpeg'
                ),
                'minWidth'  => $this->calcMin('width', $options),
                'minHeight' => $this->calcMin('height', $options)
            ));
        }

        return $constraints;
    }

    /**
     * Calculates and returns values for the size constraint
     *
     * @param $option
     * @param array $options
     * @return int
     */
    public function calcMin($option, array $options)
    {
        $min = (isset($options[$option]) ? (int)$options[$option] : 0);

        if (isset($options['instances']) && is_array($options['instances'])) {
            foreach ($options['instances'] as $instance) {
                $value = (isset($instance[$option]) ? (int)$instance[$option] : 0);

                if ($value > $min) {
                    $min = $value;
                }
            }
        }

        return $min;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['max_canvas_width']  = (int)$options['max_canvas_width'];
        $view->vars['max_canvas_height'] = (int)$options['max_canvas_height'];

        $view->vars['validate'] = $options['validate'];
        $view->vars['minimums'] = array(
            'width'  => $this->calcMin('width', $options),
            'height' => $this->calcMin('height', $options)
        );
    }
}