<?php
/**
 * @file
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CategoryController extends Controller
{
    /**
     * @Route("/category/{slug}", name="category_show")
     * @Method("GET")
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()
                   ->getManager();
        $category = $em->getRepository('AppBundle:Category')
                       ->findOneBySlug($slug);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find ategory entity.');
        }

        $category->setActiveJobs(
            $em->getRepository('AppBundle:Job')
               ->getActiveJobs($category->getId())
        );

        return $this->render('category/show.html.twig', ['category' => $category]);
    }
}
