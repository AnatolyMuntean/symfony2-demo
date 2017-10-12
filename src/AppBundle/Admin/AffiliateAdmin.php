<?php
/**
 * @file
 */

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class AffiliateAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_sort_order' => 'ASC',
        '_sort_by'    => 'is_active',
    ];

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('isActive')
               ->add('email')
               ->add('url');
    }

    protected function configureListFields(ListMapper $list)
    {
        $actions = [
            'actions' => [
                'activate'   => [
                    'template' => 'affiliateAdmin/list_action_activate.html.twig',
                ],
                'deactivate' => [
                    'template' => 'affiliateAdmin/list_action_deactivate.html.twig',
                ],
            ],
        ];

        $list->addIdentifier('email')
             ->add('url')
             ->add('isActive')
             ->add('_action', 'actions', $actions);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->add('activate', $this->getRouterIdParameter().'/activate');
        $collection->add('deactivate', $this->getRouterIdParameter().'/deactivate');

        $collection->remove('create');
    }
}
