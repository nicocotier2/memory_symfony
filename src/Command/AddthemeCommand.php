<?php
// src/Command/AddThemeCommand.php

namespace App\Command;

use App\Entity\Theme;
use App\Entity\Carte;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:addtheme',
    description: 'Ajoute un nouveau thème et des images issues de Pixabay à la base de données',
)]
class AddThemeCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private HttpClientInterface $httpClient;
    private string $pixabayApiKey;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $httpClient)
    {
        $pixabayApiKey = '46303644-470943764ecda1a7b10c7d540';
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
        $this->pixabayApiKey = $pixabayApiKey;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('nom_theme', InputArgument::REQUIRED, 'Nom du thème')
            ->addArgument('query', InputArgument::REQUIRED, 'Mot-clé pour rechercher des images sur Pixabay')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Nombre d\'images à ajouter', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $themeName = $input->getArgument('nom_theme');
        $query = $input->getArgument('query');
        $limit = $input->getOption('limit');

        // Créer un nouveau thème
        $theme = new Theme();
        $theme->setNomTheme($themeName);

        // Rechercher des images via l'API Pixabay
        $response = $this->httpClient->request('GET', 'https://pixabay.com/api/', [
            'query' => [
                'key' => $this->pixabayApiKey,
                'q' => $query,
                'image_type' => 'photo',
                'per_page' => $limit,
            ]
        ]);

        $data = $response->toArray();

        // Vérifier si des images ont été trouvées
        if (empty($data['hits'])) {
            $io->error('Aucune image trouvée pour ce mot-clé.');
            return Command::FAILURE;
        }

        // Ajouter les cartes associées avec les images de Pixabay
        foreach ($data['hits'] as $hit) {
            $carte = new Carte();
            $carte->setLien($hit['webformatURL']);
            $carte->setType("image");
            $carte->setTheme($theme);

            // Persiste la carte
            $this->entityManager->persist($carte);
        }

        // Persiste le thème et effectue l'enregistrement dans la base de données
        $this->entityManager->persist($theme);
        $this->entityManager->flush();

        $io->success("Le thème '$themeName' et ses images provenant de Pixabay ont été ajoutés avec succès.");

        return Command::SUCCESS;
    }
}
