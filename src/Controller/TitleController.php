<?php

namespace App\Controller;

use App\Entity\Constructor;
use App\Entity\Title;
use App\Form\TitleType;
use App\Repository\ConstructorRepository;
use App\Repository\TitleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TitleController extends AbstractController

{
    /**
     * @Route("/constructors-titles", name="constructors_titles", methods={"GET"})
     */
    public function index(TitleRepository $titleRepository, ConstructorRepository $constructorRepository): Response
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://ergast.com/api/f1/constructorStandings/1.json?limit=100');

        if (200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent(), true);
            $titles = $data['MRData']['StandingsTable']['StandingsLists'];

            foreach ($titles as $title) {
                $this->newFromAPI($title, $titleRepository, $constructorRepository);
            }
        }

        return $this->render('title/index.html.twig', [
            'titles' => $titleRepository->findBy(['type' => 'CONSTRUCTOR'], ['season' => 'DESC']),
        ]);
    }

    public function newFromAPI(array $object, TitleRepository $titleRepository, ConstructorRepository $constructorRepository)
    {
        $title = new Title();

        $title->setSeason($object['season']);
        $title->setType("CONSTRUCTOR");

        $result = $titleRepository->findOneBy(['type' => $title->getType(), 'season' => $title->getSeason()]);
        if (!$result) {
            $title->setRound($object['round']);
            $title->setPoints($object['ConstructorStandings'][0]['points']);
            $title->setWins($object['ConstructorStandings'][0]['wins']);

            // Here relation with constructor externalId
            $constructor = $constructorRepository->findOneBy(['externalId' => $object['ConstructorStandings'][0]['Constructor']['constructorId']]);
            if ($constructor) { //TODO else create new constructor
                $title->setConstructor($constructor);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($title);
                $entityManager->flush();
            } else {
                dump($object['ConstructorStandings'][0]['Constructor']['constructorId']);
            }
        }
    }

    /**
     * @Route("/constructors-standings", name="constructors_standings", methods={"GET"})
     */
    public function constructorsByTitles(TitleRepository $titleRepository): Response
    {
        return $this->render('title/constructorsByTitles.html.twig', [
            'constructors' => $titleRepository->countConstructorsTitles()
        ]);
    }
}
