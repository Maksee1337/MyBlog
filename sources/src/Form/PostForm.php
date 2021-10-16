<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PostForm
 *
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 *
 * @package App\Form
 */
class PostForm extends AbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * PostForm constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator, UrlGeneratorInterface $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return void //судя по всему он ничего не возвращает, а работает по ссылке с объектом $builder
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $this->translator;

        $builder->add('Short', TextType::class, ['label' => $translator->trans('Short description')]);
        $builder->add('Text', TextareaType::class, ['label' => $translator->trans('Full description')]);
        $builder->add('Author', TextType::class, ['label' => $translator->trans('Author')]);
        $builder->add('Submit', SubmitType::class, ['label' => $translator->trans('Submit')]);

        if ($options['data']->getId()) { // если пришел айди в запросе, значит это редактирование, выведем кнопку удалить
            $deleteUrl = $this->router->generate('News_DeletePost', ['post' => $options['data']->getId()]);
            $builder->add('Delete', SubmitType::class, [
                'attr' => [
                    'formaction' => $deleteUrl,
                ],
            ]);
        }
    }
}
