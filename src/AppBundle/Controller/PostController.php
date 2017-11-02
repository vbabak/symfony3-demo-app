<?php

declare(strict_types = 1);

namespace AppBundle\Controller;

use AppBundle\DependencyInjection\Pagination;
use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PostController extends Controller
{
    /**
     * @Route("/post/{page}", name="post_page", defaults={"page" = 1}, requirements={"page": "\d+"})
     */
    public function indexAction($page, Request $request)
    {
        settype($page, 'int');
        // $page = (int)$request->get('page', 1); // get parameter
        $em = $this->getDoctrine()->getManager();
        /** @var \AppBundle\Repository\PostRepository $post_repository */
        $post_repository = $em->getRepository(Post::class);
        /** @var \AppBundle\Utils\Pagination\PaginationAbstract $paginatorService */
        $paginationService = $this->get('pagination'); // from services.yml
        $paginationService->setCurrentPage($page);
        $data = $post_repository->paginate($paginationService, [], ['id' => 'desc']);

        return $this->render('post/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'items' => $data['items'],
            'links_range' => $data['links_range'],
            'current_page' => $data['current_page'],
            'total_pages' => $data['total_pages'],
        ]);
    }

    /**
     * @Route("/post-create/{id}", name="post_create_page", defaults={"id" = null}, requirements={"id": "\d+"})
     */
    public function createAction($id, Request $request)
    {
        settype($id, 'int');
        $em = $this->getDoctrine()->getManager();
        /** @var \AppBundle\Repository\PostRepository $post_repository */
        $post_repository = $em->getRepository(Post::class);
        if ($id > 0) {
            $post = $post_repository->findOneById($id);
        }
        if (empty($post)) {
            $post = new Post();
        }

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('text', TextareaType::class)
            ->add('save', SubmitType::class, array('label' => $post->getId() ? 'Update' : 'Create'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('post_page');
        }

        return $this->render('post/create.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'form' => $form->createView(),
        ]);
    }

}
