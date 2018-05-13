<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Service\RedmineService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('pages/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/projects", name="projects")
     */
    public function projectsAction(Request $request, RedmineService $redmineService)
    {
        $client = $redmineService->getClient();
        $response = $client->project->all();
        $projects = $response['projects'] ?? [];

        return $this->render('pages/projects.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route("/projects/{id}", requirements={"id" = "\d+"}, name="project")
     */
    public function projectAction(Request $request, RedmineService $redmineService, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(['projectId' => $id]);
        $client = $redmineService->getClient();
        $response = $client->project->show($id);
        $project = $response['project'] ?? [];

        $formLogWork = $this->get('form.factory')->createNamedBuilder('form_log_work')
            ->add('date', DateType::class, [
                'input' => 'string',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',])
            ->add('hours', IntegerType::class, ['attr' => ['max' => 100]])
            ->add('comment', TextareaType::class)
            ->setMethod('post')
            ->setAction('/projects/' . $id)
            ->getForm();

        $formComment = $this->get('form.factory')->createNamedBuilder('form_comment')
            ->add('comment', TextareaType::class)
            ->setMethod('post')
            ->setAction('/projects/' . $id)
            ->getForm();



        if ($request->request->has('form_comment')) {
            $dateFormComment = $request->get('form_comment');

            $comment = new Comment();
            $comment->setProjectId($id);
            $comment->setUserName($this->getUser()->getUsername());
            $comment->setComment($dateFormComment['comment']);

            $em->persist($comment);
            $em->flush();
        }

        if ($request->request->has('form_log_work')) {
            $dateFormLogWork = $request->get('form_log_work');

            $redmineService->getClient()->time_entry->create([
                'issue_id' => $id,
                'spent_on' => $dateFormLogWork['date'],
                'hours' => (float)$dateFormLogWork['hours'],
                'comments' => $dateFormLogWork['comment']
            ]);
        }

        return $this->render('pages/project.html.twig', [
            'form_log_work' => $formLogWork->createView(),
            'form_comment' => $formComment->createView(),
            'project' => $project,
            'comments' => $comments,
        ]);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route("/issues", name="issues")
     */
    public function issuesAction(Request $request, RedmineService $redmineService)
    {
        $client = $redmineService->getClient();
        $issues = $client->issue->all()['issues'];

        if ($request->getMethod() === 'POST') {
            $hours = $request->get('hours');
            $dateTime = $request->get('datetime');
            $id = $request->get('id');
            $comment = $request->get('comment');

            $client->time_entry->create([
                'issue_id' => $id,
                'spent_on' => $dateTime,
                'hours' => $hours,
                'comments' => $comment,
            ]);
        }

        return $this->render('pages/issues.html.twig', [
            'issues' => $issues,
        ]);
    }
    /**
     * @Route("/issues/{id}", requirements={"id" = "\d+"}, name="issue")
     */
    public function issueAction(Request $request, RedmineService $redmineService, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class)->findBy(['issueId' => $id]);
        $client = $redmineService->getClient();
        $response = $client->issue->show($id);
        $issue = $response['issue'] ?? [];

        $formLogWork = $this->get('form.factory')
            ->createNamedBuilder('form_log_work')
            ->add('date', DateType::class, [
                'input' => 'string',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',])
            ->add('hours', IntegerType::class, ['attr' => ['max' => 100]])
            ->add('comment', TextareaType::class)
            ->setMethod('post')
            ->setAction('/issues/' . $id)
            ->getForm();

        $formComment = $this->get('form.factory')
            ->createNamedBuilder('form_comment')
            ->add('comment', TextareaType::class)
            ->setMethod('post')
            ->setAction('/issues/' . $id)
            ->getForm();

        if ($request->request->has('form_comment')) {
            $dateFormComment = $request->get('form_comment');

            $comment = new Comment();
            $comment->setIssueId($id);
            $comment->setUserName($this->getUser()->getUsername());
            $comment->setComment($dateFormComment['comment']);

            $em->persist($comment);
            $em->flush();
        }

        if ($request->request->has('form_log_work')) {
            $dateFormLogWork = $request->get('form_log_work');

            $redmineService->getClient()->time_entry->create([
                'issue_id' => $id,
                'spent_on' => $dateFormLogWork['date'],
                'hours' => (float)$dateFormLogWork['hours'],
                'comments' => $dateFormLogWork['comment']
            ]);
        }

        return $this->render('pages/issue.html.twig', [
            'issue' => $issue,
            'comments' => $comments,
            'form_comment' => $formComment->createView(),
            'form_log_work' => $formLogWork->createView(),
        ]);
    }
}
