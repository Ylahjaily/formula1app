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

    /*
    /**
     * @Route("/new", name="circuit_new", methods={"GET","POST"})
     *./
    public function new(Request $request): Response
    {
        $circuit = new Circuit();
        $form = $this->createForm(CircuitType::class, $circuit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($circuit);
            $entityManager->flush();

            return $this->redirectToRoute('circuit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('circuit/new.html.twig', [
            'circuit' => $circuit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="circuit_show", methods={"GET"})
     *./
    public function show(Circuit $circuit): Response
    {
        return $this->render('circuit/show.html.twig', [
            'circuit' => $circuit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="circuit_edit", methods={"GET","POST"})
     *./
    public function edit(Request $request, Circuit $circuit): Response
    {
        $form = $this->createForm(CircuitType::class, $circuit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('circuit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('circuit/edit.html.twig', [
            'circuit' => $circuit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="circuit_delete", methods={"POST"})
     *./
    public function delete(Request $request, Circuit $circuit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$circuit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($circuit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('circuit_index', [], Response::HTTP_SEE_OTHER);
    }
    */
}
