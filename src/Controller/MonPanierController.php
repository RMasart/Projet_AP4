<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

final class MonPanierController extends AbstractController
{
    #[Route('/mon_panier', name: 'app_mon_panier')]
    public function index(): Response
    {
        

        $articles = []; // Define and assign a value to $articles

        return $this->render('mon_panier/index.html.twig', [
            'controller_name' => 'MonPanierController',
            'articles' => $articles, // Passe les données des articles au template index.html.twig
        ]);
    }

    #[Route('/mon_panier/supprimer/{id}', name: 'app_supprimer_article')]
    public function supprimerArticle($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        if (isset($panier[$id])) {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);
        return $this->redirectToRoute('app_panier');
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

    #[Route('/panier', name: 'panier')]
    public function panier(Request $request): Response
    {
        $produits = $request->request->get('produits', null);
        $quantites = $request->request->get('quantites', null);


        return $this->render('panier/index.html.twig', [
            'produits' => $produits,
            'quantites' => $quantites,
        ]);
    }
}