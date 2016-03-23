<?php

namespace PhotoBlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use PhotoBlogBundle\Entity\PhotoPost;
use PhotoBlogBundle\Form\PhotoPostType;

/**
 * PhotoPost controller.
 *
 * @Route("/photopost")
 */
class PhotoPostController extends Controller
{
    /**
     * Lists all PhotoPost entities.
     *
     * @Route("/", name="photopost_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $photoPosts = $em->getRepository('PhotoBlogBundle:PhotoPost')->findAll();

        return $this->render('photopost/index.html.twig', array(
            'photoPosts' => $photoPosts,
        ));
    }

    /**
     * Creates a new PhotoPost entity.
     *
     * @Route("/new", name="photopost_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $photoPost = new PhotoPost();
        $form = $this->createForm('PhotoBlogBundle\Form\PhotoPostType', $photoPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($photoPost);
            $em->flush();

            return $this->redirectToRoute('photopost_show', array('id' => $photoPost->getId()));
        }

        return $this->render('photopost/new.html.twig', array(
            'photoPost' => $photoPost,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PhotoPost entity.
     *
     * @Route("/{id}", name="photopost_show")
     * @Method("GET")
     */
    public function showAction(PhotoPost $photoPost)
    {
        $deleteForm = $this->createDeleteForm($photoPost);

        return $this->render('photopost/show.html.twig', array(
            'photoPost' => $photoPost,
            'absolutePath'=>$photoPost->getAbsolutePath(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PhotoPost entity.
     *
     * @Route("/{id}/edit", name="photopost_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PhotoPost $photoPost)
    {
        $deleteForm = $this->createDeleteForm($photoPost);
        $editForm = $this->createForm('PhotoBlogBundle\Form\PhotoPostType', $photoPost);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($photoPost);
            $em->flush();

            return $this->redirectToRoute('photopost_edit', array('id' => $photoPost->getId()));
        }

        return $this->render('photopost/edit.html.twig', array(
            'photoPost' => $photoPost,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a PhotoPost entity.
     *
     * @Route("/{id}", name="photopost_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PhotoPost $photoPost)
    {
        $form = $this->createDeleteForm($photoPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($photoPost);
            $em->flush();
        }

        return $this->redirectToRoute('photopost_index');
    }

    /**
     * Creates a form to delete a PhotoPost entity.
     *
     * @param PhotoPost $photoPost The PhotoPost entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PhotoPost $photoPost)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('photopost_delete', array('id' => $photoPost->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
