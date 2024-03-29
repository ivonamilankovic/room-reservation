<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route ("/home", name="app_home")
     */

    public function index() :Response
    {
        return $this->render('home/home.html.twig');
    }

}