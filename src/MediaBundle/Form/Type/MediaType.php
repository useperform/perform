<?php

namespace Perform\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Perform\BaseBundle\Asset\AssetContainer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Perform\MediaBundle\Entity\File;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaType extends AbstractType
{
    protected $assets;
    protected $normalizer;

    public function __construct(AssetContainer $assets, NormalizerInterface $normalizer)
    {
        $this->assets = $assets;
        $this->normalizer = $normalizer;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => 'PerformMediaBundle:File',
            'choice_label' => 'name',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // validate the file is allowed to be added
        $id = $view->vars['id'];
        $file = $view->vars['data'];
        $fileJs = $file instanceof File ?
                json_encode($this->normalizer->normalize($file))
                : '{}';
        $js = <<<EOF
Perform.media.form.media({
    el: "#media_type_{$id}",
    input: "#{$id}",
file: {$fileJs},
});
EOF;
        $this->assets->addInlineJs($js);
    }

    public function getParent()
    {
        return EntityType::class;
    }

    public function getBlockPrefix()
    {
        return 'perform_media';
    }
}
