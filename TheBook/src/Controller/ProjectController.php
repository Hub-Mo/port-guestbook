<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\TheBook;



class ProjectController extends AbstractController
{
    // getting all info from database
    #[Route('/thebook', name: 'app_TheBook', methods:["GET"])]
    public function index(ManagerRegistry $doctrine): Response
    {
        $messages = $doctrine
            ->getRepository(TheBook::class)
            ->findAll();
   
        $data = [];
   
        foreach ($messages as $message) {
           $data[] = [
               'id' => $message->getId(),
               'name' => $message->getName(),
               'description' => $message->getDescription(),
           ];
        }
   
   
        return $this->json($data);
    }

    // add new comment
    #[Route('/thebook', name: 'new_message', methods:["POST"])]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
   
        $project = new TheBook();
        $project->setName($request->request->get('name'));
        $project->setMessage($request->request->get('description'));
   
        $entityManager->persist($project);
        $entityManager->flush();
   
        return $this->json('Created new project successfully with id ' . $project->getId());
    }

    // show functionality
    #[Route('/thebook/{id}', name: 'show_message', methods:["GET"])]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $project = $doctrine->getRepository(TheBook::class)->find($id);
   
        if (!$project) {
   
            return $this->json('No project found for id' . $id, 404);
        }
   
        $data =  [
            'id' => $project->getId(),
            'name' => $project->getName(),
            'description' => $project->getDescription(),
        ];
           
        return $this->json($data);
    }

    #[Route('/thebook/{id}', name: 'show_message', methods:["PUT", "PATCH"])]
    public function edit(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $project = $entityManager->getRepository(TheBook::class)->find($id);
   
        if (!$project) {
            return $this->json('No project found for id' . $id, 404);
        }
         
        $content = json_decode($request->getContent());
        $project->setName($content->name);
        $project->setDescription($content->description);
        $entityManager->flush();
   
        $data =  [
            'id' => $project->getId(),
            'name' => $project->getName(),
            'description' => $project->getDescription(),
        ];
           
        return $this->json($data);
    }

    #[Route('/thebook/{id}', name: 'show_message', methods:["DELETE"])]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $project = $entityManager->getRepository(TheBook::class)->find($id);
   
        if (!$project) {
            return $this->json('No project found for id' . $id, 404);
        }
   
        $entityManager->remove($project);
        $entityManager->flush();
   
        return $this->json('Deleted a project successfully with id ' . $id);
    }
}
