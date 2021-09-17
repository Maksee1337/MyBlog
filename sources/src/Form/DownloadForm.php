<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * DownloadForm constructor.
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $textUrl = $this->router->generate('DownloadFile_Text', ['post' => $options['data']->getId()]);
        $htmlUrl = $this->router->generate('DownloadFile_Html', ['post' => $options['data']->getId()]);

        $builder->setMethod('GET');
        $builder->add('Text', SubmitType::class, [
            'attr' => [
                'formaction' => $textUrl,
            ],
        ]);
        $builder->add('Html', SubmitType::class, [
            'attr' => [
                'formaction' => $htmlUrl,
            ],
        ]);
    }
}
