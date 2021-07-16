<?php

namespace App\Controller;

use App\Entity\Circuit;
use App\Repository\CircuitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/circuit")
 */
class CircuitController extends AbstractController
{
    /**
     * @Route("/", name="circuit_index", methods={"GET"})
     */
    public function index(CircuitRepository $circuitRepository): Response
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://ergast.com/api/f1/circuits.json');

        if (200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent(), true);
            $circuits = $data['MRData']['CircuitTable']['Circuits'];

           foreach ($circuits as $circuit) {
               $this->newFromAPI($circuit, $circuitRepository);
           }
        }

        return $this->render('circuit/index.html.twig', [
            'circuits' => $circuitRepository->findAll(),
        ]);
    }

    public function newFromAPI(array $object, CircuitRepository $circuitRepository)
    {
        $circuit = new Circuit();

        $circuit->setName($object['circuitName']);
        $circuit->setCountry($object['Location']['country']);
        $circuit->setLocality($object['Location']['locality']);
        $circuit->setExternalId($object['circuitId']);

        $result = $circuitRepository->findOneBy(['externalId' => $circuit->getExternalId()]);
        if (!$result) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($circuit);
            $entityManager->flush();
        }
    }

}
