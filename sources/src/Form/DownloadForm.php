<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DownloadForm
 *
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 *
 * @package App\Form
 */
class DownloadForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');
        $builder->add('Text', SubmitType::class, [
            'attr' => [
                'formaction' => '/DownloadFile/'.$options['data']->getId().'/text',
            ],
        ]);
        $builder->add('Html', SubmitType::class, [
            'attr' => [
                'formaction' => '/DownloadFile/'.$options['data']->getId().'/html',
            ],
        ]);

    }
}
