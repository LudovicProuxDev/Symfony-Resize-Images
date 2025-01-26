<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
final class DocumentController extends AbstractController
{
    #[Route(name: 'app_document_index', methods: ['GET'])]
    public function index(DocumentRepository $documentRepository): Response
    {
        return $this->render('document/index.html.twig', [
            'documents' => $documentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_document_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $images */
            $images = $form->get('documents')->getData();
            // Limit number of files
            if (count($images) > 3) { // Example: Limit to 3 files
                $this->addFlash('error', 'You can upload a maximum of 3 files.');
                return $this->redirectToRoute('app_document_new');
            }

            foreach ($images as $image) {
                $originalName = $fileUploader->upload($image);
                $originalSize = filesize($fileUploader->getUploadsDirectory() . '/' . $originalName);
                // Get the uploded file from the folder '/uploads'
                $newImage = new File($fileUploader->getUploadsDirectory() . '/' . $originalName);
                $newName = $fileUploader->uploadResize($newImage);
                $newSize = filesize($fileUploader->getUploadsDirectory() . '/' . $newName);
                // Create the record
                $newDocument = new Document();
                $newDocument->setOriginalName($originalName);
                $newDocument->setOriginalSize($originalSize);
                $newDocument->setNewName($newName);
                $newDocument->setNewSize($newSize);
                $entityManager->persist($newDocument);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('document/new.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }
}
