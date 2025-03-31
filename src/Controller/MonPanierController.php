<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class MonPanierController extends AbstractController
{
    #[Route('/mon_panier', name: 'app_mon_panier')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        // Récupérer les articles correspondants depuis la base de données
        $articles = [];
        if (!empty($panier)) {
            $articles = $entityManager->getRepository(ArticleController::class)->findBy(['id' => $panier]);
        }

        return $this->render('mon_panier/index.html.twig', [
            'controller_name' => 'MonPanierController',
            'articles' => $articles, // On passe bien la variable articles au template
        ]);
    }


    #[Route('/mon_panier/confirmation', name: 'app_confirmation_panier')]
    public function confirmationPanier(SessionInterface $session, ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
        $panier = $session->get('panier', []);
        $articles = $articleRepository->findBy(['id' => $panier]);

        if (empty($articles)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_mon_panier');
        }

        // Calculer le total
        $total = array_reduce($articles, function ($sum, $article) {
            return $sum + $article->getPrix();
        }, 0);

        // Créer un nouvel achat
        $achat = new Achat();
        $achat->setDateAchat(new \DateTime());
        $achat->setTotal($total);

        foreach ($articles as $article) {
            $achat->addArticle($article);
        }

        $user = $this->getUser();
        if ($user) {
            $achat->setUser($user);
        }

        $entityManager->persist($achat);
        $entityManager->flush();

        // Vider le panier après la confirmation
        $session->set('panier', []);

        $this->addFlash('success', 'Votre commande a été confirmée.');

        return $this->render('mon_panier/confirmation.html.twig', [
            'articles' => $articles,
            'total' => $total,
        ]);
    }

    #[Route('/historique', name: 'app_historique')]
   
    public function historique(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $achats = $entityManager->getRepository(Achat::class)->findBy(['user' => $user]);

        return $this->render('historique/index.html.twig', [
            'achats' => $achats,
        ]);
    }
}
