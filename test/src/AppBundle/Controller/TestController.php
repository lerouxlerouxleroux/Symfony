<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class TestController extends Controller
{

    /**
     * @Route("/test                                                                                                         ", name="testpage") //annotation, associe les methodes à des routes
     */
    public function testAction(Request $request) //$request est l'instantioation de la classe Request
    
    {
        $res = new Response ('<html><head></head><body><p>Test réussi</p></body></html>');
        return $res;
    }                                                                                                                         }