<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MonPanierController extends AbstractController
{
    #[Route('/mon_panier', name: 'app_mon_panier')]
    public function index(): Response
    {
        // Tableau d'articles
        $articles = [
            [
                'id' => 1,
                'nom' => 'Article 1',
                'prix' => 10,
                'disponible' => true,
            ],
            [
                'id' => 2,
                'nom' => 'Article 2',
                'prix' => 15,
                'disponible' => true,
            ],
            [
                'id' => 3,
                'nom' => ' Article 3',
                'prix' => 0, 
                'disponible' => false,
            ],
            [
                'id' => 4,
                'nom' => 'Article 4',
                'prix' => 20,
                'disponible' => true,
            ],
        ];

        return $this->render('mon_panier/index.html.twig', [
            'controller_name' => 'MonPanierController',
            'produits' => $articles, // Passe les données des article au template index.html.twig
        ]);
    }

    #[Route('/mon_panier/confirmation', name: 'app_confirmation_panier')]
    public function confirmation(): Response
    {
        return $this->render('mon_panier/confirmation.html.twig', [
            'controller_name' => 'MonPanierController',
        ]);
    }

    public function confirm(): Response
    {
        return $this->redirectToRoute('app_confirmation_panier');
    }
}