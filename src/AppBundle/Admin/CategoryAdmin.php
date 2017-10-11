<?php
/**
 * @file
 */

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class CategoryAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_sort_order' => 'ASC',
        '_sort_by'    => 'name',
    ];

    protected function configureFormFields(FormMapper $form)
    {
        $form->add('name')
             ->add('slug');
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('name');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->add('name')
             ->add('slug');
    }
}
