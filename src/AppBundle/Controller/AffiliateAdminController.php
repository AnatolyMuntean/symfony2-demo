<?php
/**
 * @file
 */

namespace AppBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AffiliateAdminController extends Controller
{
    public function activateAction($id)
    {
        if (false === $this->admin->isGranted('EDIT')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        /** @var \AppBundle\Entity\Affiliate $affiliate */
        $affiliate = $em->getRepository('AppBundle:Affiliate')->findOneById($id);

        try {
            $affiliate->setIsActive(true);
            $em->flush();
        }
        catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', $e->getMessage());

            return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    public function deactivateAction($id)
    {
        if (false === $this->admin->isGranted('EDIT')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        /** @var \AppBundle\Entity\Affiliate $affiliate */
        $affiliate = $em->getRepository('AppBundle:Affiliate')->findOneById($id);

        try {
            $affiliate->setIsActive(false);
            $em->flush();
        }
        catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', $e->getMessage());

            return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }
}
