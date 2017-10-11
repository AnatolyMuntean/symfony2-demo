<?php
/**
 * @file
 */

namespace AppBundle\Admin;

use AppBundle\Entity\Job;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class JobAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by'    => 'expiresAt',
    ];

    protected function configureFormFields(FormMapper $form)
    {
        $form->add('category')
             ->add(
                 'type',
                 'choice',
                 [
                     'choices'  => Job::getTypes(),
                     'expanded' => true,
                 ]
             )
             ->add('company')
             ->add(
                 'file',
                 'file',
                 [
                     'label'    => 'Company logo',
                     'required' => false,
                 ]
             )
             ->add('url')
             ->add('position')
             ->add('location')
             ->add('description')
             ->add('howToApply')
             ->add('isPublic')
             ->add('email')
             ->add('isActivated');
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('category')
               ->add('company')
               ->add('position')
               ->add('description')
               ->add('isActivated')
               ->add('isPublic')
               ->add('email')
               ->add('expiresAt');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('company')
             ->add('position')
             ->add('location')
             ->add('url')
             ->add('isActivated')
             ->add('email')
             ->add('category')
             ->add('expiresAt')
             ->add(
                 '_action',
                 'actions',
                 [
                     'actions' => [
                         'view'   => [],
                         'edit'   => [],
                         'delete' => [],
                     ],
                 ]
             );
    }

    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('category')
            ->add('type')
            ->add('company')
            ->add('logo', 'string', ['template' => 'jobAdmin/list_image.html.twig'])
            ->add('url')
            ->add('position')
            ->add('location')
            ->add('description')
            ->add('howToApply')
            ->add('isPublic')
            ->add('isActivated')
            ->add('token')
            ->add('email')
            ->add('expiresAt');
    }

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        if ($this->hasRoute('edit') && $this->isGranted('EDIT') && $this->hasRoute('delete') && $this->isGranted('DELETE')) {
            $actions['extend'] = [
                'label'            => 'Extend',
                'ask_confirmation' => true,
            ];
        }

        return $actions;
    }
}
