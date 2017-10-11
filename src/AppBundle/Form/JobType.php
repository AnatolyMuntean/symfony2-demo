<?php

namespace AppBundle\Form;

use AppBundle\Entity\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('category')
                ->add(
                    'type',
                    'choice',
                    [
                        'choices'  => Job::getTypes(),
                        'expanded' => true,
                    ]
                )
                ->add('company')
                ->add('logo', 'file',
                    [
                        'required' => false,
                        'label'    => 'Company logo',
                    ]
                )
                ->add('url', null, ['required' => false])
                ->add('position')
                ->add('location')
                ->add('description')
                ->add('howToApply', null, ['label' => 'How to apply?'])
                ->add('isPublic', null, ['label' => 'Public?'])
                ->add('email');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\Job',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'job';
    }
}
