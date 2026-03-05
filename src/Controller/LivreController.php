<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LivreController extends AbstractController
{
    // ─── LISTE des livres ────────────────────────────────────────────────────
    #[Route('/livres', name: 'app_livre_index')]
    public function index(LivreRepository $repo): Response
    {
        $livres = $repo->findAll();

        return $this->render('livre/index.html.twig', [
            'livres' => $livres,
        ]);
    }

    // ─── VOIR un livre ───────────────────────────────────────────────────────
    #[Route('/livres/{id}', name: 'app_livre_show')]
    public function show(int $id, LivreRepository $repo): Response
    {
        $livre = $repo->find($id);

        if (!$livre) {
            throw $this->createNotFoundException('Livre introuvable.');
        }

        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    // ─── CRÉER un livre ──────────────────────────────────────────────────────
    #[Route('/livres/new', name: 'app_livre_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $livre = new Livre();
        $form  = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livre->setCreatedAt(new \DateTimeImmutable());

            // Doctrine gère automatiquement la table de jointure livre_auteur
            // On a juste besoin de persist() + flush() comme d'habitude
            $em->persist($livre);
            $em->flush();

            $this->addFlash('success', 'Livre ajouté avec succès !');

            return $this->redirectToRoute('app_livre_index');
        }

        return $this->render('livre/new.html.twig', [
            'form' => $form,
        ]);
    }

    // ─── MODIFIER un livre ───────────────────────────────────────────────────
    #[Route('/livres/{id}/edit', name: 'app_livre_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $livre = $em->getRepository(Livre::class)->find($id);

        if (!$livre) {
            throw $this->createNotFoundException('Livre introuvable.');
        }

        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Livre modifié avec succès !');

            return $this->redirectToRoute('app_livre_index');
        }

        return $this->render('livre/edit.html.twig', [
            'form'  => $form,
            'livre' => $livre,
        ]);
    }

    // ─── SUPPRIMER un livre ──────────────────────────────────────────────────
    #[Route('/livres/{id}/delete', name: 'app_livre_delete')]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $livre = $em->getRepository(Livre::class)->find($id);

        if ($livre) {
            // Doctrine supprime automatiquement les entrées dans livre_auteur
            $em->remove($livre);
            $em->flush();

            $this->addFlash('success', 'Livre supprimé.');
        }

        return $this->redirectToRoute('app_livre_index');
    }

    // ─── GÉRER les auteurs (page bonus) ─────────────────────────────────────
    #[Route('/auteurs', name: 'app_auteur_index')]
    public function auteurs(Request $request, EntityManagerInterface $em): Response
    {
        // Formulaire simple pour ajouter un auteur
        if ($request->isMethod('POST')) {
            $nom = trim($request->request->get('nom', ''));

            if ($nom) {
                $auteur = new Auteur();
                $auteur->setNom($nom);
                $em->persist($auteur);
                $em->flush();

                $this->addFlash('success', "Auteur « $nom » ajouté.");
            }

            return $this->redirectToRoute('app_auteur_index');
        }

        $auteurs = $em->getRepository(Auteur::class)->findBy([], ['nom' => 'ASC']);

        return $this->render('livre/auteurs.html.twig', [
            'auteurs' => $auteurs,
        ]);
    }
}