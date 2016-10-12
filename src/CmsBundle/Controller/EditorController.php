<?php

namespace Perform\CmsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Perform\BaseBundle\Annotation\Ajax;
use Perform\CmsBundle\Entity\Version;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * EditorController.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EditorController extends Controller
{
    /**
     * @Ajax()
     * @Route("/load/{id}")
     */
    public function loadVersionAction(Request $request, Version $version)
    {
        $sharedSections = $this->get('perform_cms.section_locator')
                        ->findCurrentSections($request->query->get('shared', []));

        return [
            'sections' => $version->toArray(),
            'sharedSections' => $sharedSections,
        ];
    }

    /**
     * @Ajax
     * @Route("/save/{id}")
     * @Method("POST")
     */
    public function saveVersionAction(Request $request, Version $version)
    {
        $sections = $request->request->get('sections');
        try {
            $this->get('perform_cms.version_updater')
                ->update($version, $sections);

            if ($version->isPublished()) {
                $this->get('perform_cms.publisher')->publishVersion($version);
            }

            return [
                'message' => sprintf('Version "%s" saved successfully.', $version->getTitle()),
            ];
        } catch (\Exception $e) {
            $this->get('logger')->error($e);

            return [
                'message' => sprintf('An error occurred saving version "%s".', $version->getTitle()),
                'code' => 500,
            ];
        }
    }

    /**
     * @Ajax
     * @Route("/publish/{id}")
     * @Method("POST")
     */
    public function publishVersionAction(Version $version)
    {
        try {
            $this->get('perform_cms.publisher')
                ->publishVersion($version);

            return [
                'message' => sprintf('Version "%s" published successfully.', $version->getTitle()),
            ];
        } catch (\Exception $e) {
            $this->get('logger')->error($e);

            return [
                'message' => sprintf('An error occurred publishing version "%s".', $version->getTitle()),
                'code' => 500,
            ];
        }
    }
}