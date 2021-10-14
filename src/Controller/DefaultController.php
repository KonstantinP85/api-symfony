<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route(path="/front", methods={"GET"}, name="app_front_default_index")
     */
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }
}