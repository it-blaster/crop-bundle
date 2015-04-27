<?php

namespace Fenrizbes\CropBundle\Behavior;

use Fenrizbes\UploadableBehavior\Behavior\UploadableBehavior;

class CroppableBehavior extends \Behavior
{
    /**
     * {@inheritdoc}
     */
    protected $tableModificationOrder = 40;

    /**
     * {@inheritdoc}
     */
    protected $parameters = array(
        'columns' => 'image'
    );

    /**
     * Array of columns
     *
     * @var array
     */
    protected $columns;

    protected function getColumns()
    {
        if (is_null($this->columns)) {
            $this->columns = array();

            foreach (explode(',', $this->getParameter('columns')) as $column) {
                $this->columns[trim($column)] = trim($column) .'_coordinates';
            }
        }

        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyTable()
    {
        $table = $this->getTable();

        foreach ($this->getColumns() as $image_column => $coordinates_column) {
            if (!$table->hasColumn($image_column)) {
                $table->addColumn(array(
                    'name' => $image_column,
                    'type' => 'VARCHAR',
                    'size' => 255
                ));
            }

            if (!$table->hasColumn($coordinates_column)) {
                $table->addColumn(array(
                    'name' => $coordinates_column,
                    'type' => 'OBJECT'
                ));
            }
        }

        $this->modifyUploadableBehavior();
    }

    /**
     * Adds or modifies the uploadable behavior
     */
    protected function modifyUploadableBehavior()
    {
        $table = $this->getTable();

        if ($table->hasBehavior('uploadable')) {
            $uploadable = $table->getBehavior('uploadable');
            $parameters = $uploadable->getParameters();
            $up_columns = explode(',', $parameters['columns']);

            foreach (array_keys($this->getColumns()) as $column) {
                foreach ($up_columns as $up_column) {
                    if (trim($up_column) == $column) {
                        continue 2;
                    }
                }

                $up_columns[] = $column;
            }

            $parameters['columns'] = implode(',', $up_columns);
            $uploadable->setParameters($parameters);
        } else {
            $uploadable = new UploadableBehavior();
            $uploadable->setName('uploadable');
            $uploadable->setParameters(array(
                'columns' => implode(',', array_keys($this->getColumns()))
            ));

            $table->addBehavior($uploadable);
        }
    }

    /**
     * Constructs methods
     *
     * @return string
     */
    public function objectMethods()
    {
        $script = '';

        foreach ($this->getColumns() as $image_column => $coordinates_column) {
            $script .= $this->renderTemplate('setCroppableData', array(
                'image_column'       => $this->getTable()->getColumn($image_column)->getPhpName(),
                'coordinates_column' => $this->getTable()->getColumn($coordinates_column)->getPhpName()
            ));

            $script .= $this->renderTemplate('getCroppableData', array(
                'image_column'       => $this->getTable()->getColumn($image_column)->getPhpName(),
                'coordinates_column' => $this->getTable()->getColumn($coordinates_column)->getPhpName()
            ));
        }

        return $script;
    }
}