<?php

namespace CL\BooksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use CL\BooksBundle\Entity\Book;
use CL\BooksBundle\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;

class BookController extends Controller
{
    /**
     * @Route("/book/showAll", name="clbook_book_showall")
     */
    public function showAllAction()
    {
        $books = $this->getDoctrine()->getRepository('CLBooksBundle:Book')->findAll();
        
        return $this->render('CLBooksBundle:Book:show_all.html.twig', array(
            'books'=>$books
        ));
    }

    /**
     * @Route("book/create", name="clbook_book_create")
     */
    public function createAction(Request $request)
    {
        $book = new Book();
        $url = $this->generateUrl('clbook_book_create');
        $form = $this->createBookForm($book,$url);
        
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $book=$form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();
            
            return $this->redirect($this->generateUrl('clbook_book_showall'));
            
        }
        
        return $this->render('CLBooksBundle:Book:create.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    private function createBookForm (Book $book, $url){
                $form = $this->createFormBuilder($book)
                      ->setAction($url)
                      ->setMethod('POST')
                      ->add('title','text')
                      ->add('description','textarea')
                      ->add('author','entity',array('class'=>'CLBooksBundle:Author','choice_label'=>'name'))
                      ->add('save','submit')
                      ->getForm();
                return $form;
    }
    /**
     * @Route("/book/edit/{id}", name="clbook_book_edit")
     */
    public function editAction(Request $req, $id) {
        $author = $this->getDoctrine()->getRepository('CLBooksBundle:Book')
                ->findOneById($id);

        $url = $this->generateUrl('clbook_book_edit', array('id' => $id));
        $form = $this->createBookForm($author, $url);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl('clbook_book_showall'));
        }
        return $this->render('CLBooksBundle:Book:edit.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("book/delete/{id}")
     */
    public function deleteAction($id)
    {
        $book = $this->getDoctrine()->getRepository('CLBooksBundle:Book')->findOneById($id);
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();
        return $this->redirect($this->generateUrl('clbook_book_showall'));
    }
        /**
     * @Route("/book/show/{id}")
     */
    public function showAction($id)
    {
        $book = $this->getDoctrine()
                       ->getRepository('CLBooksBundle:Book')
                       ->findOneById($id);
        
        return $this->render('CLBooksBundle:Author:show.html.twig', array('book'=>$book));
    }
}
