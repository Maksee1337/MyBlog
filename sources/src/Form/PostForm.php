<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return void //судя по всему он ничего не возвращает, а работает по ссылке с объектом $builder
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Short', TextType::class);
        $builder->add('Text', TextareaType::class);
        $builder->add('Author', TextType::class);
        $builder->add('Submit', SubmitType::class);

        if ($options['data']->getId()) { // если пришел айди в запросе, значит это редактирование, выведем кнопку удалить
            $builder->add('Delete', SubmitType::class);
        }
    }
}
