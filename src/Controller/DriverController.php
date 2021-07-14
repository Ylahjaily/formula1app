<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DriverController extends AbstractController
{
    /**
     * @Route("/drivers")
     */
    function getAllDriver() {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://ergast.com/api/f1/drivers.json?limit=853');

        $statusCode = $response->getStatusCode();

        if (200 === $statusCode) {
            $content = $response->getContent();
            $array = json_decode($content, true);
            $drivers = $array['MRData']['DriverTable']['Drivers'];
        }

        return $this->render('/drivers/alldrivers.html.twig',[
            'drivers' => $drivers
        ]);
    }
}
