<?php

namespace App\Controller;

use App\Repository\CarteRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class GameController extends AbstractController
{
    #[Route('/game/start', name: 'start_game')]
public function startGame(CarteRepository $carteRepository, ThemeRepository $themeRepository): Response
{
    $themeId = 4;
    $theme = $themeRepository->find($themeId);
    if (!$theme) {
        throw $this->createNotFoundException('Thème non trouvé');
    }

    // Récupération des cartes par thème
    $cartes = $carteRepository->findBy(['theme' => $theme]);

    // Duplication des cartes pour obtenir des paires
    $cartesDupliquees = [];
    foreach ($cartes as $carte) {
        $cartesDupliquees[] = $carte;
        $cartesDupliquees[] = clone $carte; // Clone pour avoir deux instances de chaque carte
    }

    // Mélange des cartes
    shuffle($cartesDupliquees);

    return $this->render('game/index.html.twig', [
        'cartes' => $cartesDupliquees,
    ]);
}

#[Route('/game/check-match', name: 'check_match', methods: ['POST'])]
public function checkMatch(Request $request, CarteRepository $carteRepository): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    // Vérifiez que les données envoyées sont valides
    if (!isset($data['first_card'], $data['second_card'])) {
        return $this->json(['error' => 'Invalid data'], 400);
    }

    $firstCard = $carteRepository;
    $secondCard = $carteRepository;

    // Vérifiez que les deux cartes existent
    if (!$firstCard || !$secondCard) {
        return $this->json(['match' => false], 404);
    }

    // Comparez les cartes pour vérifier si elles correspondent
    if ($data['first_card'] === $data['second_card']) {
        return $this->json(['match' => true]);
    }

    return $this->json(['match' => false]);
}
}