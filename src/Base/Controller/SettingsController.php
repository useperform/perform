<?php

namespace Admin\Base\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * SettingsController.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsController extends Controller
{
    /**
     * @Route("/{panel}")
     * @Template()
     */
    public function settingsAction(Request $request, $panel = null)
    {
        //fetch sort order from config, use first here
        $panel = $panel ?: 'account';
        $builder = $this->createFormBuilder();
        $registry = $this->get('admin_base.settings_panel_registry');
        if (!$registry->getPanel($panel)->isEnabled()) {
            throw new NotFoundHttpException();
        }
        $registry->getPanel($panel)->buildForm($builder);
        $form = $builder->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            //handle update
        }

        return [
            'registry' => $registry,
            'form' => $form->createView(),
            'activePanel' => $panel,
        ];
    }
}
