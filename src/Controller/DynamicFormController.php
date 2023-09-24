<?php

namespace App\Controller;

use App\Entity\Sections;
use App\Entity\Categories;
use App\Entity\Informations;
use App\Form\Type\DynamicType;
use App\Repository\SectionsRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InformationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DynamicFormController extends AbstractController
{
    #[Route('/dynamic/form', name: 'app_dynamic_form')]
    public function index(Request $request, InformationsRepository$informationsRepository, CategoriesRepository $categoriesRepository, EntityManagerInterface $entityManager): Response
    {
        $categories = $categoriesRepository->findAll(); 
        $information = new Informations();
        $informations = $informationsRepository->findAll(); 

        $form = $this->createForm(DynamicType::class, $information, ['categories' => $categories]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $information = $form->getData();

            // ... perform some action, such as saving the task to the database

            $entityManager->persist($information);
            $entityManager->flush();

            return $this->redirectToRoute('app_dynamic_form');
        }

        return $this->render('dynamic_form/index.html.twig', [
            'form' => $form,
            'informations' => $informations

        ]);
    }


    #[Route(path: '/dynamic/get-sections/{id}', name: 'get_sections', methods: ['GET'])]
    public function getSections(Categories $categories, SectionsRepository $sectionRepository): JsonResponse
    {
        $sections = $sectionRepository->findBy(['Category' => $categories->getId()], ['name' => 'ASC']);
        $sectionArray = [];

        foreach ($sections as $section) {
            $sectionArray[] = [
                'id' => $section->getId(),
                'name' => $section->getName(),
            ];
        }

        return new JsonResponse(['sections' => $sectionArray]);
    }
}
