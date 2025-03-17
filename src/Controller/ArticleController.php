<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Stocker;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\StockerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ArticleController extends AbstractController
{
    #[Route('/article/admin', name: 'app_article_admin_index', methods: ['GET'])]
    public function indexadmin(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/articleadmin.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }


    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/article/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 🔹 Gestion de l'image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                    $article->setImage($newFilename);
                } catch (FileException $e) {
                    // Gérer l'erreur si nécessaire
                }
            }

            $entityManager->persist($article);
            $entityManager->flush();

            // 🔹 Gestion de la quantité
            $quantite = $form->get('quantite')->getData();
            if ($quantite > 0) {
                $stocker = new Stocker();
                $stocker->setArticle($article);
                $stocker->setQuantite($quantite);
                $stocker->setEntrepotId(1); // À adapter selon la logique métier

                $entityManager->persist($stocker);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/article/search', 'app_search_article', methods: ['POST'])]
    public function searchArticle(ArticleRepository $article, Request $request): Response
    {

        $query = $request->request->get('s', '');

        if (empty($query)) {
            return $this->redirectToRoute('app_article_index');
        }

        $articles = $article->searchByQuery($query);

        return $this->render('article/search.html.twig', [
            'articles' => $articles,
            'query' => $query,
        ]);
    }

    #[Route('/article/g/{id}', name: 'article_detail')]
    public function show(int $id, ArticleRepository $Article, StockerRepository $stockerRepository): Response
    {
        $article = $Article->find($id);

        if (!$article) {
            throw $this->createNotFoundException("l'article n'existe pas");
        }

        // 🔹 Récupérer la quantité en stock
        $stock = $stockerRepository->findOneBy(['article' => $article]);
        $quantite = $stock ? $stock->getQuantite() : 0; // Par défaut, 0 si non trouvé
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'quantite' => $quantite, // Passer la quantité au template
        ]);
    }


    #[Route('/article/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Article $article,
        EntityManagerInterface $entityManager,
        StockerRepository $stockerRepository
    ): Response {
        // 🔹 Récupérer la quantité existante
        $stock = $stockerRepository->findOneBy(['article' => $article]);
        $quantite = $stock ? $stock->getQuantite() : 0;

        // 🔹 Ajouter la quantité au formulaire
        $form = $this->createForm(ArticleType::class, $article, [
            'quantite' => $quantite,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 🔹 Gestion de l'image si modifiée
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                    $article->setImage($newFilename);
                } catch (FileException $e) {
                    // Gérer l'erreur
                }
            }

            // 🔹 Mettre à jour la quantité
            $newQuantite = $form->get('quantite')->getData();
            if ($stock) {
                $stock->setQuantite($newQuantite);
            } else {
                $stock = new Stocker();
                $stock->setArticle($article);
                $stock->setQuantite($newQuantite);
                $stock->setEntrepotId(1);

                $entityManager->persist($stock);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'quantite' => $quantite,
        ]);
    }


    #[Route('/article/delete/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
