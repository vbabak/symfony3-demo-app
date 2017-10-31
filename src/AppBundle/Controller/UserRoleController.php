<?php

declare(strict_types = 1);

namespace AppBundle\Controller;

use AppBundle\DependencyInjection\Pagination;
use AppBundle\Entity\UserRole;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserRoleController extends Controller
{
    /**
     * @Route("/user-roles", name="user_roles_page")
     */
    public function indexAction(Request $request)
    {
        $page = (int)$request->get('page', 1);
        $em = $this->getDoctrine()->getManager();
        /** @var \AppBundle\Repository\UserRoleRepository $user_roles_repository */
        $user_roles_repository = $em->getRepository(UserRole::class);
        /** @var \AppBundle\Utils\Pagination\PaginationAbstract $paginatorService */
        $paginationService = $this->get('pagination'); // from services.yml
        $paginationService->setCurrentPage($page);
        $data = $user_roles_repository->paginate($paginationService, [], ['code' => 'asc']);

        // $data = $user_roles_repository->paginate($paginatorService, ['id' => ['value' => 1, 'comparator' => '>']], ['code' => 'asc']);

        return $this->render('user_role/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'items' => $data['items'],
            'links_range' => $data['links_range'],
            'current_page' => $data['current_page'],
        ]);
    }

}
