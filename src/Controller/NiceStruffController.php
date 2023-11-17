<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NiceStruffController extends AbstractController
{
    /**
     * @Route("/nice/struff", name="app_nice_struff")
     */
    public function index(): Response
    {
        return $this->render('nice_struff/index.html.twig', [
            'controller_name' => 'NiceStruffController',
        ]);
    }
}
