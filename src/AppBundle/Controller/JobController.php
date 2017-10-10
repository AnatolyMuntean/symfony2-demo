<?php
/**
 * @file
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Job controller.
 *
 * @Route("job")
 */
class JobController extends Controller
{
    /**
     * Lists all job entities.
     *
     * @Route("/", name="job_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()
                   ->getManager();

        $categories = $em->getRepository('AppBundle:Category')
                         ->getWithJobs();

        $maxJobsOnPage = $this->container->getParameter('max_jobs_on_homepage');
        foreach ($categories as $category) {
            $category->setActiveJobs(
                $em->getRepository('AppBundle:Job')
                   ->getActiveJobs($category->getId(), $maxJobsOnPage)
            );
            $category->setMoreJobs(
                $em->getRepository('AppBundle:Job')
                   ->countActiveJobs($category->getId()) - $this->container->getParameter('max_jobs_on_homepage')
            );
        }

        return $this->render(
            'job/index.html.twig',
            [
                'categories' => $categories,
            ]
        );
    }

    /**
     * Creates a new job entity.
     *
     * @Route("/new", name="job_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $job = new Job();
        $form = $this->createForm('AppBundle\Form\JobType', $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()
                       ->getManager();
            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('job_show', ['id' => $job->getId()]);
        }

        return $this->render(
            'job/new.html.twig',
            [
                'job'  => $job,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a job entity.
     *
     * @Route("/job/{company}/{location}/{id}/{position}", name="job_show", requirements={"id" = "\d+"})
     * @ParamConverter("job", options={"repository_method" = "getActiveJob"})
     * @Method("GET")
     */
    public function showAction(Job $job)
    {
        $deleteForm = $this->createDeleteForm($job);

        return $this->render(
            'job/show.html.twig',
            [
                'job'         => $job,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing job entity.
     *
     * @Route("/{id}/edit", name="job_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Job $job)
    {
        $deleteForm = $this->createDeleteForm($job);
        $editForm = $this->createForm('AppBundle\Form\JobType', $job);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()
                 ->getManager()
                 ->flush();

            return $this->redirectToRoute('job_edit', ['id' => $job->getId()]);
        }

        return $this->render(
            'job/edit.html.twig',
            [
                'job'         => $job,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Deletes a job entity.
     *
     * @Route("/{id}", name="job_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Job $job)
    {
        $form = $this->createDeleteForm($job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()
                       ->getManager();
            $em->remove($job);
            $em->flush();
        }

        return $this->redirectToRoute('job_index');
    }

    /**
     * Creates a form to delete a job entity.
     *
     * @param Job $job The job entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Job $job)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('job_delete', ['id' => $job->getId()]))
                    ->setMethod('DELETE')
                    ->getForm();
    }
}