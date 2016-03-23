<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 3/23/2016
 * Time: 2:07 PM
 */

namespace CategoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Doctrine\DBAL\Query;

class ShowCategory extends Controller
{
    /**
     * @Route("/categories/{name}", name="show_category")
     */
    public function showCategoryAction($name){
        $em=$this->getDoctrine()->getManager();
        $categories = $em->getRepository('CategoryBundle:Category')->findBy(array(), array('id'=>'asc'));

        $blogsRep = $this->getDoctrine()->getRepository('BlogBundle:Blog');
        $query=$blogsRep->createQueryBuilder('b')
            ->where('b.category=:name')
            ->setParameter('name',$name)
            ->orderBy('b.publishedDate','desc')
            ->getQuery();
        $blogs=$query->getResult();

        return $this->render('CategoryBundle:default:index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'blogs'=>$blogs,
            'categories'=>$categories
        ]);

    }

}