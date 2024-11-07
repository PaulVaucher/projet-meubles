<?php
// src/Controller/MeublesController.php
namespace App\Controller;

// les require
use App\Entity\Meubles; //la table meubles
use Doctrine\ORM\EntityManagerInterface; //gestionnaire de db
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\MeublesRepository;
use Symfony\Component\HttpFoundation\Request;


class MeublesController extends AbstractController
{
    // remplir la table meubles de la DB, pour le moment avec des données en dur
    #[Route('/add-meubles', name: 'create_meubles')]
    public function create_meubles(EntityManagerInterface $entityManager): Response
    {
        $meuble = new Meubles();
        $meuble->setType('meuble test');
        $meuble->setPrix(1337);
        $meuble->setCouleur('couleur');
        $meuble->setMatiere('matière');
        $meuble->setDimensions('X x Y x Z');


        // tell Doctrine you want to (eventually) save the meubles (no queries yet)
        $entityManager->persist($meuble);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new meuble with id '.$meuble->getId());
    }

    // modifier les données d'une entrée en DB
    #[Route('/change-meubles/{id}', name: 'modify_meubles')]
    public function modify_meubles (EntityManagerInterface $entityManager, string $id): Response 
    {
        $meuble = $entityManager->getRepository(Meubles::class)->find($id);

        // Vérifier si l'entité existe
        if (!$meuble) {
            return new Response('Meuble non trouvé', Response::HTTP_NOT_FOUND);
        }
        
        // Valeurs à modifier pour le formulaire 
        $meuble->getId($id);
        $meuble->setType('meuble retest');
        $meuble->setPrix(665);
        $meuble->setCouleur('blouge');
        $meuble->setMatiere('spectrale');
        $meuble->setDimensions('1 x 1 x 1');

        $entityManager->flush();

        return new Response('Meuble '.$meuble->getId().' modifié');
    }

    // effacer une entrée en DB
    #[Route('/remove-meubles/{id}', name: 'remove_meubles')]
    public function remove_meubles (EntityManagerInterface $entityManager, string $id): Response
    {
        $meuble = $entityManager->getRepository(Meubles::class)->find($id);

        // Vérifier si l'entité existe
        if (!$meuble) {
            return new Response('Meuble non trouvé', Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($meuble);
        $entityManager->flush();

        return new Response('Meuble '.$meuble->getId().' supprimé');
    }
}