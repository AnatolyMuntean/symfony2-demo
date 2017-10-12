<?php
/**
 * @file
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    /**
     * @Route("/category/{slug}/{page}", defaults={"page": 1}, name="category_show")
     * @Method("GET")
     */
    public function showAction(Request $request, $slug, $page)
    {
        $em = $this->getDoctrine()
                   ->getManager();
        $category = $em->getRepository('AppBundle:Category')
                       ->findOneBySlug($slug);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find ategory entity.');
        }

        $total_jobs = $em->getRepository('AppBundle:Job')
                         ->countActiveJobs($category->getId());
        $jobs_per_page = $this->container->getParameter('max_jobs_on_category');
        $last_page = ceil($total_jobs / $jobs_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $category->setActiveJobs(
            $em->getRepository('AppBundle:Job')
               ->getActiveJobs($category->getId(), $jobs_per_page, ($page - 1) * $jobs_per_page)
        );

        $format = $request->getRequestFormat();

        /** @var \Symfony\Bundle\FrameworkBundle\Routing\Router $router */
        $router = $this->get('router');

        return $this->render(
            'category/show.'.$format.'.twig',
            [
                'category'      => $category,
                'last_page'     => $last_page,
                'previous_page' => $previous_page,
                'current_page'  => $page,
                'next_page'     => $next_page,
                'total_jobs'    => $total_jobs,
                'feed_id'       => sha1($router->generate('category_show', ['slug' => $category->getSlug(), '_format' => 'atom'], true))
            ]
        );
    }
}
