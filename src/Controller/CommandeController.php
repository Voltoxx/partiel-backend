<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Material;
use App\Form\CommandeType;
use App\Entity\CommandeMaterial;
use App\Repository\CommandeRepository;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MaterialRepository $materialRepository): Response
    {
        $commande = new Commande();
        $materials = $materialRepository->findAll();

        $form = $this->createForm(CommandeType::class, $commande, [
            'materials' => $materials,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($materials as $material) {
                $quantity = $form->get('material_' . $material->getId())->getData();
                
                if ($quantity > 0) {
                    $commandeMaterial = new CommandeMaterial();
                    $commandeMaterial->setMaterial($material);
                    $commandeMaterial->setQuantite($quantity);
                    $commande->addCommandeMaterial($commandeMaterial);

                    // Déduire la quantité du stock
                    $material->setQuantite($material->getQuantite() - $quantity);
                }
            }

            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
        }

        return $this->render('commande/new.html.twig', [
            'form' => $form->createView(),
            'materials' => $materials,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $materials = $entityManager->getRepository(Material::class)->findAll();
        
        $form = $this->createForm(CommandeType::class, $commande, [
            'materials' => $materials,
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $submittedData = [];
    
            foreach ($materials as $material) {
                $fieldName = 'material_' . $material->getId();
                if ($form->has($fieldName)) {
                    $quantity = $form->get($fieldName)->getData();
                    if ($quantity > 0) {
                        $submittedData[$material->getId()] = $quantity;
                    }
                }
            }
    
            // Récupération des matériaux originaux de la commande
            $originalMaterials = $entityManager->getRepository(CommandeMaterial::class)->findBy(['commande' => $commande]);
    
            // Suppression des matériaux non soumis
            foreach ($originalMaterials as $originalMaterial) {
                if (!array_key_exists($originalMaterial->getMaterial()->getId(), $submittedData)) {
                    $material = $originalMaterial->getMaterial();
                    $material->setQuantite($material->getQuantite() + $originalMaterial->getQuantite());
                    $entityManager->persist($material);
    
                    $commande->removeCommandeMaterial($originalMaterial);
                    $entityManager->remove($originalMaterial);
                }
            }
    
            // Ajout ou mise à jour des matériaux soumis
            foreach ($submittedData as $materialId => $quantity) {
                $material = $entityManager->getRepository(Material::class)->find($materialId);
                if ($material) {
                    $existingCommandeMaterial = $commande->getCommandeMaterials()->filter(function ($item) use ($material) {
                        return $item->getMaterial() === $material;
                    })->first();
    
                    if ($existingCommandeMaterial) {
                        $existingCommandeMaterial->setQuantite($quantity);
                    } else {
                        $commandeMaterial = new CommandeMaterial();
                        $commandeMaterial->setMaterial($material);
                        $commandeMaterial->setQuantite($quantity);
                        $commande->addCommandeMaterial($commandeMaterial);
                    }
    
                    $material->setQuantite($material->getQuantite() - $quantity);
                    $entityManager->persist($material);
                    if (!$existingCommandeMaterial) {
                        $entityManager->persist($commandeMaterial);
                    }
                }
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
            'materials' => $materials,
        ]);
    }    

    #[Route('/{id}/rendre', name: 'app_commande_rendre', methods: ['POST'])]
    public function rendre(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        foreach ($commande->getCommandeMaterials() as $commandeMaterial) {
            $material = $commandeMaterial->getMaterial();
            $material->setQuantite($material->getQuantite() + $commandeMaterial->getQuantite());
        }
        $entityManager->remove($commande);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_commande_index');
    }

}
