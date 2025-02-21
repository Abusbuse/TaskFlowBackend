<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

#[Route('/api/task', name: 'task_')]
class TaskController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    // Injection des services via le constructeur
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    #[Route('', methods: ['GET'])]
    public function getTasks(): JsonResponse
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findAll();
        return $this->json($tasks, 200, [], ['groups' => 'task:read']);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getTask(Task $task): JsonResponse
    {
        return $this->json($task, 200, [], ['groups' => 'task:read']);
    }

    #[Route('', methods: ['POST'])]
    public function createTask(Request $request): JsonResponse
    {
        // Décoder les données JSON
        $data = json_decode($request->getContent(), true);

        // Trouver l'utilisateur par son ID
        $user = $this->userRepository->find($data['user_id']);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Créer la tâche
        $task = new Task();
        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $task->setStatus($data['status']);
        $task->setUser($user);  // Assigner l'utilisateur à la tâche

        // Sauvegarder la tâche
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Task created successfully'], 201);
    }
}
