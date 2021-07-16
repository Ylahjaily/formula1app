<?php

namespace App\Controller;

use App\Entity\Title;
use App\Form\TitleType;
use App\Repository\TitleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/title")
 */
class TitleController extends AbstractController
{
    /**
     * @Route("/standings-constructor", name="constructors_standings", methods={"GET"})
     */
    public function index(TitleRepository $titleRepository): Response
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://ergast.com/api/f1/constructorStandings/1.json');

        if (200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent(), true);
            $titles = $data['MRData']['StandingsTable']['StandingsList'];

            foreach ($titles as $title) {
                $this->newFromAPI($titles, $titleRepository);
            }
        }

        return $this->render('title/index.html.twig', [
            'titles' => $titleRepository->findAll(),
        ]);
    }

    public function newFromAPI(array $object, TitleRepository $titleRepository)
    {
        $title = new Title();

        $title->setSeason($object['season']);
        $title->setRound($object['round']);
        $title->setType("CONSTRUCTOR");
        $title->setPoints($object['points']);
        $title->setWins($object['wins']);

        // Here relation with constructor externalId
        

        $result = $titleRepository->findOneBy(['externalId' => $title->getExternalId()]);
        if (!$result) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($title);
            $entityManager->flush();
        }
    }
}
