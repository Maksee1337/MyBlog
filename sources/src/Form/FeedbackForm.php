<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

/**
 * Class PostForm
 *
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 *
 * @package App\Form
 */
class FeedbackForm extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return void //судя по всему он ничего не возвращает, а работает по ссылке с объектом $builder
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Name', TextType::class, ['label' => 'Enter Your Name']);
        $builder->add('Text', TextareaType::class, ['label' => 'Enter Your Text']);
        $builder->add('Email', EmailType::class, ['label' => 'Enter Your Email Address']);
        $builder->add('Submit', SubmitType::class);
    }
}
