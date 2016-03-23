<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\DBAL\Query;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/profiles/{firstName}.{lastName}", requirements={
     *     "firstName": "[a-zA-Z]+", "lastName": "[a-zA-Z]+"
     *     })
     */
    public function indexAction($firstName, $lastName)
    {
        $categories = $this->getDoctrine()->getRepository('CategoryBundle:Category')->findBy(array(), array('id'=>'asc'));

        $username = $firstName.$lastName;
        $repository = $this->getDoctrine()->getRepository('UserBundle:User');
        $query = $repository->createQueryBuilder('u')
            ->where('u.username=:username')
            ->setParameter('username',$username)
            ->getQuery();
        $my_user=$query->getResult();

        $profilePic=$my_user[0]->getProfilePic();
        return $this->render('UserBundle:Default:index.html.twig',
            array(
                'firstName'=>$firstName,
                'lastName'=>$lastName,
                'profilePic'=>$profilePic,
                'categories' => $categories
            ));
    }

    /**
     * @Route("/profile/{username}", name="show_profile")
     */
    public function profileAction($username){
        $categories = $this->getDoctrine()->getRepository('CategoryBundle:Category')->findBy(array(), array('id'=>'asc'));

        $repository = $this->getDoctrine()->getRepository('UserBundle:User');
        $query = $repository->createQueryBuilder('u')
            ->where('u.username=:username')
            ->setParameter('username',$username)
            ->getQuery();
        $my_user=$query->getResult();
        $firstName=$my_user[0]->getFirstName();
        $lastName=$my_user[0]->getLastName();
        $profilePic=$my_user[0]->getProfilePic();

        return $this->render('UserBundle:Default:index.html.twig',
            array(
                'firstName'=>$firstName,
                'lastName'=>$lastName,
                'profilePic'=>$profilePic,
                'categories'=>$categories
            ));
    }

    /**
     * @Route("/editprofile/{username}", name="edit_profile")
     */
    public function editProfileAction(Request $request, $username){
        $categories = $this->getDoctrine()->getRepository('CategoryBundle:Category')->findBy(array(), array('id'=>'asc'));

        $repository = $this->getDoctrine()->getRepository('UserBundle:User');
        $query = $repository->createQueryBuilder('u')
            ->where('u.username=:username')
            ->setParameter('username', $username)
            ->getQuery();
        $users=$query->getResult();
        $user=$users[0];

        $editForm = $this->createForm('UserBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setUsername();

            // $file stores the uploaded PDF file
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $user->getProfilePic();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            $picsDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/pics';
            $file->move($picsDir, $fileName);

            // Update the 'profilePic' property to store the image file name
            // instead of its contents
            $user->setProfilePic($fileName);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('show_profile', array('username' => $user->getUsername()));
        }

        return $this->render('UserBundle:Default:editprofile.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'categories' => $categories
        ));
    }

    /**
     * @Route("/changeprofilepicture/{username}", name="change_profile_picture")
     */
    public function changeProfilePictureAction(Request $request, $username){
        $categories = $this->getDoctrine()->getRepository('CategoryBundle:Category')->findBy(array(), array('id'=>'asc'));

        $repository = $this->getDoctrine()->getRepository('UserBundle:User');
        $query = $repository->createQueryBuilder('u')
            ->where('u.username=:username')
            ->setParameter('username', $username)
            ->getQuery();
        $users=$query->getResult();
        $user=$users[0];

        $editForm = $this->createForm('UserBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setUsername();

            // $file stores the uploaded PDF file
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $user->getProfilePic();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            $picsDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/pics';
            $file->move($picsDir, $fileName);

            // Update the 'profilePic' property to store the image file name
            // instead of its contents
            $user->setProfilePic($fileName);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('show_profile', array('username' => $user->getUsername()));
        }

        return $this->render('UserBundle:Default:editprofile.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'categories' => $categories
        ));
    }
}
