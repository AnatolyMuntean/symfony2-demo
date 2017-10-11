<?php
/**
 * @file
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Job controller.
 *
 * @Route("/job")
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

            return $this->redirectToRoute(
                'job_preview',
                [
                    'token'    => $job->getToken(),
                    'company'  => $job->getCompanySlug(),
                    'location' => $job->getLocationSlug(),
                    'position' => $job->getPositionSlug(),
                ]
            );
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
     * @Route("/{company}/{location}/{id}/{position}", name="job_show", requirements={"id" = "\d+"})
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
     * @Route("/{token}/edit", name="job_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Job $job)
    {
        if ($job->getIsActivated()) {
            throw $this->createNotFoundException('Job is activated and cannot be edited.');
        }

        $deleteForm = $this->createDeleteForm($job);
        $editForm = $this->createForm('AppBundle\Form\JobType', $job);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()
                 ->getManager()
                 ->flush();

            return $this->redirectToRoute(
                'job_preview',
                [
                    'token'    => $job->getToken(),
                    'company'  => $job->getCompanySlug(),
                    'location' => $job->getLocationSlug(),
                    'position' => $job->getPositionSlug(),
                ]
            );
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
     * @Route("/{token}/delete", name="job_delete")
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
                    ->setAction($this->generateUrl('job_delete', ['token' => $job->getToken()]))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * @Route("/{company}/{location}/{token}/{position}", name="job_preview")
     * @ParamConverter("job", options={"exclude": {"company","location","position"}})
     * @Method("GET")
     */
    public function previewAction(Job $job)
    {
        $deleteForm = $this->createDeleteForm($job);
        $publishForm = $this->createPublishForm($job);
        $extendForm = $this->createExtendForm($job);

        return $this->render(
            'job/show.html.twig',
            [
                'job'          => $job,
                'delete_form'  => $deleteForm->createView(),
                'publish_form' => $publishForm->createView(),
                'extend_form' => $extendForm->createView(),
            ]
        );
    }

    /**
     * @Route("/{token}/publish", name="job_publish")
     * @Method("POST")
     */
    public function publishAction(Request $request, Job $job)
    {
        $form = $this->createPublishForm($job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()
                       ->getManager();
            $job->publish();
            $em->persist($job);
            $em->flush();

            $this->addFlash('notice', 'Job is online for 30 days.');
        }

        return $this->redirectToRoute(
            'job_preview',
            [
                'token'    => $job->getToken(),
                'company'  => $job->getCompanySlug(),
                'location' => $job->getLocationSlug(),
                'position' => $job->getPositionSlug(),
            ]
        );
    }

    private function createPublishForm(Job $job)
    {
        return $this->createFormBuilder(['token' => $job->getToken()])
                    ->add('token', 'hidden')
                    ->setMethod('POST')
                    ->getForm();
    }

    public function extendAction(Request $request, Job $job)
    {
        $form = $this->createExtendForm($job);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()
                       ->getManager();

            if (!$job->extend()) {
                throw $this->createNotFoundException('Unable to extend the job.');
            }

            $em->persist($job);
            $em->flush();

            $this->addFlash(
                'notice',
                sprintf(
                    'Job validity extended until %s.',
                    $job->getExpiresAt()
                        ->format('Y-m-d')
                )
            );
        }

        return $this->redirectToRoute(
            'job_preview',
            [
                'token'    => $job->getToken(),
                'company'  => $job->getCompanySlug(),
                'location' => $job->getLocationSlug(),
                'position' => $job->getPositionSlug(),
            ]
        );
    }

    private function createExtendForm(Job $job)
    {
        return $this->createFormBuilder(['token' => $job->getToken()])
                    ->add('token', 'hidden')
                    ->setMethod('POST')
                    ->getForm();
    }
}
