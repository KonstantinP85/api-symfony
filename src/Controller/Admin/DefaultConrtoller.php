<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app_admin_default_index")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}