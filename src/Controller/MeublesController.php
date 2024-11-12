<?php
// src/Controller/MeublesController.php
namespace App\Controller;

// Les require : on importe les classes nécessaires
use App\Entity\Meubles; // Importation de la classe Meubles (l'entité qui représente un meuble)
use Doctrine\ORM\EntityManagerInterface; // Gestionnaire de la base de données, utilisé pour interagir avec la DB
use Symfony\Component\HttpFoundation\Response; // Pour renvoyer des réponses HTTP classiques
use Symfony\Component\HttpFoundation\JsonResponse; // Pour renvoyer des réponses au format JSON
use Symfony\Component\Routing\Annotation\Route; // Permet d'utiliser les annotations pour définir les routes
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Classe de base des contrôleurs Symfony
use Symfony\Component\HttpFoundation\Request; // Permet d'obtenir des données depuis la requête HTTP (méthodes POST, PUT, etc.)
use App\Repository\MeublesRepository; // Importation de la classe Repository pour interagir avec la base de données

class MeublesController extends AbstractController
{


#[Route('/meubles', name: 'meubles_list')] // La route pour afficher la liste des meubles depuis la BDD
public function listMeubles(MeublesRepository $meublesRepository): Response
{
    // Récupérer tous les meubles de la base de données
    $meubles = $meublesRepository->findAll(); // findAll() est une méthode de la classe Repository qui renvoie tous les meubles

    // Renvoyer à une vue Twig (une page HTML) pour afficher la liste des meubles
    return $this->render('meubles/list.html.twig', [
        'meubles' => $meubles
    ]);
}


    // 1. Ajouter un meuble à la base de données
    #[Route('/add-meubles', name: 'create_meubles', methods: ['GET','POST'])] // La route pour ajouter un meuble avec méthode POST
    public function create_meubles(EntityManagerInterface $entityManager): JsonResponse // La fonction pour ajouter un meuble
    {
        // On crée une nouvelle instance de l'entité Meubles
        $meuble = new Meubles();
        
        // On remplit les propriétés de l'entité avec des données (ici en dur)
        $meuble->setType('meuble test'); // Le type du meuble
        $meuble->setPrix(1337); // Le prix du meuble
        $meuble->setCouleur('couleur'); // La couleur du meuble
        $meuble->setMatiere('matière'); // La matière du meuble
        $meuble->setDimensions('X x Y x Z'); // Les dimensions du meuble

        // On informe Doctrine qu'on souhaite persister cette entité (pas encore dans la DB, juste en préparation)
        $entityManager->persist($meuble);

        // Cette ligne exécute réellement l'insertion SQL dans la base de données
        $entityManager->flush();

        // Une fois le meuble inséré, on renvoie une réponse JSON avec les informations du meuble créé
        return new JsonResponse([
            'message' => 'Meuble ajouté avec succès', // Message de confirmation
            'id' => $meuble->getId(), // Retourne l'ID du meuble inséré
            'type' => $meuble->getType(), // Type du meuble
            'prix' => $meuble->getPrix(), // Prix du meuble
            'couleur' => $meuble->getCouleur(), // Couleur du meuble
            'matiere' => $meuble->getMatiere(), // Matière du meuble
            'dimensions' => $meuble->getDimensions() // Dimensions du meuble
        ], Response::HTTP_CREATED); // On retourne le statut HTTP 201 (créé)
    }

    // 2. Modifier un meuble existant
    #[Route('/change-meubles/{id}', name: 'modify_meubles', methods: ['GET','PUT'])] //  La route pour modifier un meuble avec méthode PUT
    public function modify_meubles(EntityManagerInterface $entityManager, string $id): JsonResponse
    {
        // On cherche le meuble en utilisant l'ID passé dans l'URL
        $meuble = $entityManager->getRepository(Meubles::class)->find($id);

        // Si le meuble n'est pas trouvé dans la base de données
        if (!$meuble) {
            // On retourne une réponse JSON avec un message d'erreur et un code HTTP 404 (Non trouvé)
            return new JsonResponse([
                'message' => 'Meuble non trouvé'
            ], Response::HTTP_NOT_FOUND);
        }

        // Mise à jour des propriétés du meuble
        $meuble->setType('meuble retest'); // Nouveau type
        $meuble->setPrix(665); // Nouveau prix
        $meuble->setCouleur('blouge'); // Nouvelle couleur
        $meuble->setMatiere('spectrale'); // Nouvelle matière
        $meuble->setDimensions('1 x 1 x 1'); // Nouvelles dimensions

        // Doctrine va mettre à jour l'enregistrement dans la base de données
        $entityManager->flush();

        // Une fois les modifications enregistrées, on renvoie une réponse JSON avec les nouvelles informations
        return new JsonResponse([
            'message' => 'Meuble modifié avec succès', // Message de confirmation
            'id' => $meuble->getId(), // ID du meuble modifié
            'type' => $meuble->getType(), // Nouveau type du meuble
            'prix' => $meuble->getPrix(), // Nouveau prix
            'couleur' => $meuble->getCouleur(), // Nouvelle couleur
            'matiere' => $meuble->getMatiere(), // Nouvelle matière
            'dimensions' => $meuble->getDimensions() // Nouvelles dimensions
        ]);
    }

    // 3. Supprimer un meuble de la base de données
    #[Route('/remove-meubles/{id}', name: 'remove_meubles', methods: ['GET','DELETE'])] // La route pour supprimer un meuble avec methode DELETE
    public function remove_meubles(EntityManagerInterface $entityManager, string $id): JsonResponse
    {
        // On cherche le meuble dans la base de données avec l'ID fourni
        $meuble = $entityManager->getRepository(Meubles::class)->find($id);

        // Si le meuble n'existe pas, on renvoie une erreur 404
        if (!$meuble) {
            return new JsonResponse([
                'message' => 'Meuble non trouvé'
            ], Response::HTTP_NOT_FOUND);
        }

        // Si le meuble est trouvé, on le supprime de la base de données
        $entityManager->remove($meuble);

        // On effectue la suppression dans la base de données
        $entityManager->flush();

        // Une fois le meuble supprimé, on retourne une réponse JSON pour informer de la suppression
        return new JsonResponse([
            'message' => 'Meuble supprimé avec succès', // Message de confirmation
            'id' => $id // L'ID du meuble supprimé
        ]);
    }
}
