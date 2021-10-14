<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UnSubscribeForm
 *
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 *
 * @package App\Form
 */
class UnSubscribeForm extends AbstractType
{
    /**
     * @var TranslatorInterface
     */


    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Email', EmailType::class, ['label' => 'Enter Your Email Address']);
        $builder->add('Submit', SubmitType::class, ['label' => 'Unsubscribe']);
    }
}
