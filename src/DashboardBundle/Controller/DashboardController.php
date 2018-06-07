<?php

namespace Perform\DashboardBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Perform\BaseBundle\Panel\PanelInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DashboardController extends Controller
{
    /**
     * @Route()
     * @Template
     */
    public function indexAction()
    {
        $panels = [];
        foreach (['left' => 'panels.left', 'right' => 'panels.right'] as $side => $parameter) {
            $panels[$side] = [];
            foreach ($this->getParameter('perform_dashboard.'.$parameter, []) as $service) {
                $panel = $this->get($service);
                if (!$panel instanceof PanelInterface) {
                    throw new \InvalidArgumentException(sprintf('Panel service "%s" must be an instance of Perform\DashboardBundle\Panel\PanelInterface, %s given', $service, get_class($panel)));
                }

                $panels[$side][] = $panel;
            }
        }

        return [
            'panels' => $panels,
        ];
    }
}
