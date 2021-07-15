<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;
class ConstructorController extends AbstractController
{
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

        dump($results_array);


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

        dump($results_array2);

        $unique_value =  array_unique($results_array2, SORT_REGULAR);

        $nbWins = array_column($unique_value, '1', '0');
        $topWins = array_multisort($nbWins, SORT_DESC, $unique_value);

        dump($nbWins);

        return $this->render('/constructors/mostWinningRace.html.twig', [
            'season' => $season,
            'constructors' => $nbWins
        ]);
    }
}
