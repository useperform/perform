<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
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
        $panelName = $panel ?: 'account';
        $builder = $this->createFormBuilder();
        $registry = $this->get('perform_base.settings_panel_registry');
        $manager = $this->get('perform_base.settings_manager');
        $panel = $registry->getPanel($panelName);
        if (!$panel->isEnabled()) {
            throw new NotFoundHttpException();
        }
        $panel->buildForm($builder, $manager);
        $form = $builder->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            //handle update
            try {
                $panel->handleSubmission($form, $manager);
                $this->addFlash('success', 'Settings updated.');

                return $this->redirectToRoute('perform_base_settings_settings', ['panel' => $panelName]);
            } catch (\Exception $e) {
                $this->addFlash('danger', 'An error occurred.');
            }
        }

        return [
            'registry' => $registry,
            'form' => $form->createView(),
            'activePanel' => $panelName,
        ];
    }
}
