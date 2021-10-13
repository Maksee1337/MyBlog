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
 * Class PostForm
 *
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 *
 * @package App\Form
 */
class FeedbackForm extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FeedbackForm constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $this->translator;

        $builder->add('Name', TextType::class, ['label' => 'Enter Your Name']); // работает и так
        $builder->add('Text', TextareaType::class, ['label' => $translator->trans('Enter Your Text')]);
        $builder->add('Email', EmailType::class, ['label' => $translator->trans('Enter Your Email Address')]);
        $builder->add('Submit', SubmitType::class, ['label' => $translator->trans('Submit')]);
    }
}
