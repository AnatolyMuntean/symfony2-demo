<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Affiliate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Affiliate controller.
 *
 * @Route("affiliate")
 */
class AffiliateController extends Controller
{
    /**
     * Creates a new affiliate entity.
     *
     * @Route("/new", name="affiliate_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $affiliate = new Affiliate();
        $form = $this->createForm('AppBundle\Form\AffiliateType', $affiliate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()
                       ->getManager();
            $em->persist($affiliate);
            $em->flush();

            return $this->redirectToRoute('affiliate_wait');
        }

        return $this->render(
            'affiliate/new.html.twig',
            [
                'affiliate' => $affiliate,
                'form'      => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/wait", name="affiliate_wait")
     * @Method({"GET"})
     */
    public function waitAction()
    {
        return $this->render('affiliate/wait.html.twig');
    }

    /**
     * Creates a form to delete a affiliate entity.
     *
     * @param Affiliate $affiliate The affiliate entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Affiliate $affiliate)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('affiliate_delete', ['id' => $affiliate->getId()]))
                    ->setMethod('DELETE')
                    ->getForm();
    }
}
