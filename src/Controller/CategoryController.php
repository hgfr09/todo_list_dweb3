<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'category_index')]
    public function index(CategoryRepository $repo): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $repo->findAll()
        ]);
    }
    
    #[Route('/categories/new', name: 'category_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'La catégorie a été ajoutée avec succès.');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/categories/delete', name: 'category_delete')]
    public function delete(Request $request, CategoryRepository $repo, EntityManagerInterface $em): Response
    {
        $id = $request->getPayload()->get('id');
        $cat = $repo->find($id);

        if ($cat == null) {
            throw $this->createNotFoundException();
        }

        $em->remove($cat);
        $em->flush();

        $this->addFlash('success', 'La catégorie a été supprimée avec succès.');
        
        return $this->redirectToRoute('category_index');

    }

    #[Route('/categories/{id}', name: 'category_show')]
    public function show(Category $cat): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $cat
        ]);
    }

    #[Route('/categories/{id}/edit', name: 'category_edit')]
    public function edit(Category $cat, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La catégorie a été modifiée avec succès.');

            return $this->redirectToRoute('category_show', ['id' => $cat->getId()]);
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form
        ]);
    }
}
