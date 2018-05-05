<?php

namespace Perform\RichContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Perform\BaseBundle\Asset\AssetContainer;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RichContentType extends AbstractType
{
    protected $assets;

    public function __construct(AssetContainer $assets)
    {
        $this->assets = $assets;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => 'PerformRichContentBundle:Content',
            'choice_label' => 'id',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // validate the file is allowed to be added
        $id = $view->vars['id'];
        $file = $view->vars['data'];
        $js = <<<EOF
Perform.richContent.form.richContent({
  el: "#rich_content_type_{$id}",
  input: "#{$id}",
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
        return 'perform_rich_content';
    }
}
