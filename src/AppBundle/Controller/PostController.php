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
     * @Route("/post", name="post_page")
     */
    public function indexAction(Request $request)
    {
        $page = (int)$request->get('page', 1);
        $em = $this->getDoctrine()->getManager();
        /** @var \AppBundle\Repository\PostRepository $post_repository */
        $post_repository = $em->getRepository(Post::class);
        /** @var \AppBundle\Utils\Pagination\PaginationAbstract $paginatorService */
        $paginationService = $this->get('pagination'); // from services.yml
        $paginationService->setCurrentPage($page);
        $data = $post_repository->paginate($paginationService, [], ['code' => 'asc']);

        return $this->render('post/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'items' => $data['items'],
            'links_range' => $data['links_range'],
            'current_page' => $data['current_page'],
        ]);
    }

    /**
     * @Route("/post-create", name="post_create_page")
     */
    public function createAction(Request $request)
    {$em = $this->getDoctrine()->getManager();
        /** @var \AppBundle\Repository\PostRepository $post_repository */
        $post_repository = $em->getRepository(Post::class);
        $post = new Post();

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('text', TextareaType::class)
            ->add('save', SubmitType::class, array('label' => 'Create'))
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
