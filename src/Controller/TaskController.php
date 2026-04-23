<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('/', name: 'task_index')]
    public function index(Request $request, TaskRepository $repo, EntityManagerInterface $em): Response
    {
        $task = new Task;
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $task->setIsDone(false);
            $task->setCreatedAt(new \DateTimeImmutable());
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été ajoutée avec succès.');

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/index.html.twig', [
            'tasks' => $repo->findBy([], ['createdAt' => 'DESC']),
            'form' => $form
        ]);
    }

    #[Route('/tasks/complete', name: 'task_complete')]
    public function complete(Request $request, TaskRepository $repo, EntityManagerInterface $em): Response
    {
        $id = $request->getPayload()->get('id');
        $task = $repo->find($id);

        if ($task === null) {
            throw $this->createNotFoundException();
        }

        $task->setIsDone(!$task->isDone());
        $em->flush();

        $this->addFlash('success', 'La tâche a étée modifiée avec succès.');

        return $this->redirectToRoute('task_index');
    }

    #[Route('/tasks/delete', name: 'task_delete')]
    public function delete(Request $request, TaskRepository $repo, EntityManagerInterface $em): Response
    {
        $id = $request->getPayload()->get('id');
        $task = $repo->find($id);

        if ($task === null) {
            throw $this->createNotFoundException();
        }

        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a été supprimée avec succès.');

        return $this->redirectToRoute('task_index');
    }
}
