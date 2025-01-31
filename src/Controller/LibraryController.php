<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Book;
use App\Repository\BookRepository;

class LibraryController extends AbstractController
{
    /**
     * @Route("/library", name="app_library")
     */
    public function index(
        BookRepository $bookRepository
    ): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('library/index.html.twig', [
            'controller_name' => 'LibraryController',
            "books" => $books
        ]);
    }
    
    /**
     * @Route("/library/create", name="book_create", methods={"GET"})
     */
    public function createBook(
    ): Response
    {
        return $this->render('library/create.html.twig', [
            'controller_name' => 'LibraryController'
        ]);
    }

    /**
     * @Route("/library/create", name="book_create_post", methods={"POST"})
     */
    public function createBookPost(
        Request $request,
        ManagerRegistry $doctrine
    ): Response
    {
        $entityManager = $doctrine->getManager();

        $book = new Book();
        $book->setName($request->request->get("name"));
        $book->setIsbn(intval($request->request->get("isbn")));
        $book->setAuthor($request->request->get("author"));
        $book->setImage($request->request->get("image"));

        $entityManager->persist($book);
        $entityManager->flush();

        return $this->redirectToRoute("app_library");
    }

    /**
     * @Route("/library/{id}", name="book_single")
     */
    public function singleBook(
        BookRepository $bookRepository,
        int $id
    ): Response
    {
        $book = $bookRepository->find($id);

        return $this->render('library/single.html.twig', [
            'controller_name' => 'LibraryController',
            "book" => $book
        ]);
    }

    /**
     * @Route("/library/delete/{id}", name="book_delete", methods={"GET"})
     */
    public function deleteBook(
        BookRepository $bookRepository,
        int $id
    ): Response
    {
        $book = $bookRepository->find($id);

        return $this->render('library/delete.html.twig', [
            'controller_name' => 'LibraryController',
            "book" => $book
        ]);
    }

    /**
     * @Route("/library/delete/{id}", name="book_delete_post", methods={"POST"})
     */
    public function deleteBookPost(
        ManagerRegistry $doctrine,
        int $id
    ): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException(
                "No book found for id ".$id
            );
        }

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('app_library');
    }

    /**
     * @Route("/library/update/{id}", name="book_update", methods={"GET"})
     */
    public function updateBook(
        BookRepository $bookRepository,
        int $id
    ): Response
    {
        $book = $bookRepository->find($id);

        return $this->render('library/update.html.twig', [
            'controller_name' => 'LibraryController',
            "book" => $book
        ]);
    }

    /**
     * @Route("/library/update/{id}", name="book_update_post", methods={"POST"})
     */
    public function updateBookPost(
        ManagerRegistry $doctrine,
        Request $request,
        int $id
    ): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException(
                "No book found for id ".$id
            );
        }

        $book->setName($request->request->get("name"));
        $book->setIsbn(intval($request->request->get("isbn")));
        $book->setAuthor($request->request->get("author"));
        $book->setImage($request->request->get("image"));

        $entityManager->flush();

        return $this->redirectToRoute('app_library');
    }
}
