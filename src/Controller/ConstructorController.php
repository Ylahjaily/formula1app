<?php


namespace App\Controller;

use App\Entity\Constructor;
use App\Repository\ConstructorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class ConstructorController extends AbstractController
{
    /**
     * @Route("/constructors", name="constructor_index", methods={"GET"})
     */
    public function index(ConstructorRepository $constructorRepository): Response
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://ergast.com/api/f1/constructors.json');

        if (200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent(), true);
            $constructors = $data['MRData']['ConstructorTable']['Constructors'];

            foreach ($constructors as $constructor) {
                $this->newFromAPI($constructor, $constructorRepository);
            }
        }

        return $this->render('constructors/index.html.twig', [
            'constructors' => $constructorRepository->findAll(),
        ]);
    }

    public function newFromAPI(array $object, ConstructorRepository $constructorRepository)
    {
        $constructor = new Constructor();

        $constructor->setName($object['name']);
        $constructor->setNationality($object['nationality']);
        $constructor->setExternalId($object['constructorId']);

        $result = $constructorRepository->findOneBy(['externalId' => $constructor->getExternalId()]);
        if (!$result) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($constructor);
            $entityManager->flush();
        }
    }

    /**
     * @Route("/constructors/most/winning/{season}", name="constructors_most_winning_race_in_season",  methods={"GET"})
     */
    function getConstructorWhitMostWinnigRaceBySeason ($season= "2020") {

        $httpClient = HttpClient::create();
        $url = "http://ergast.com/api/f1/".$season."/results.json?limit=1000";
        $response = $httpClient->request('GET', $url);
        $statusCode = $response->getStatusCode();

        if (200 === $statusCode) {
            $content = $response->getContent();
            $array = json_decode($content, true);
            $data = $array['MRData']['RaceTable']['Races'];
        }

        $results_array = [];

        foreach ($data as $item) {
            $results =  $item['Results'];

            foreach($results as $key => $result) {

                if ($result['position'] === "1") {

                    $results_array[] = $results[$key]['Constructor'];
                }
            }
        }


        $results_array2 = [];

        foreach ($results_array as $key => $result) {
            $count = 0;
            $constructor_id= $result['constructorId'];
            $constructor_name =  $result['name'];

            foreach ($results_array as $result2) {
                if($constructor_id === $result2['constructorId']) {
                    $count = $count +1;
                }
            }
            $results_array2[] = [$constructor_name, $count];
        }

        $unique_value =  array_unique($results_array2, SORT_REGULAR);

        $nbWins = array_column($unique_value, '1', '0');
        $topWins = array_multisort($nbWins, SORT_DESC, $unique_value);

        return $this->render('/constructors/mostWinningRace.html.twig', [
            'season' => $season,
            'constructors' => $nbWins
        ]);
    }

}
