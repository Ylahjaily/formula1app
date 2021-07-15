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
 * @Route("/drivers", name="list_all_drivers",  methods={"GET"})
 */
    function getAllDriver() {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://ergast.com/api/f1/drivers.json?limit=853');

        $statusCode = $response->getStatusCode();

        if (200 === $statusCode) {
            $content = $response->getContent();
            $array = json_decode($content, true);
            $data = $array['MRData']['DriverTable']['Drivers'];
        }

        return $this->render('/drivers/alldrivers.html.twig',[
            'drivers' => $data
        ]);
    }

    /**
     * @Route("/drivers/most/winning/{season}", name="drivers_most_winning_race_in_season",  methods={"GET"})
     */
    function getDriverWhitMostWinnigRaceBySeason($season="2021") {
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

                    $results_array[] = $results[$key]['Driver'];
                }
            }
        }

        $results_array2 = [];

        foreach ($results_array as $key => $result) {
            $count = 0;
            $driver_code = $result['code'];
            $driver_name =  $result['givenName'].' '.$result['familyName'];

            foreach ($results_array as $result2) {
                if($driver_code === $result2['code']) {
                    $count = $count +1;
                }
            }
            $results_array2[] = [$driver_name, $count];
        }

        $unique_value =  array_unique($results_array2, SORT_REGULAR);

        $nbWins = array_column($unique_value, '1', '0');
        $topWins = array_multisort($nbWins, SORT_DESC, $unique_value);

        return $this->render('/drivers/mostWinningRace.html.twig', [
            'season' => $season,
            'drivers' => $nbWins
        ]);
    }
}
