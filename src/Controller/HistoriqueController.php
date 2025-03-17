<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CommandeRepository;


final class HistoriqueController extends AbstractController
{
    #[Route('/historique', name: 'historique')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Ajouté le critère du fait de récupérer seulement les commandes de l'utilisateur !
        // Ajout d'une FK entre l'utilisateur et la commande pour identifier la commande de l'utilisateur

        $commandes = $commandeRepository->findBy([], ['dateCommande' => 'DESC']);

        return $this->render('historique/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }
}