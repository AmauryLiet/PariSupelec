<?php

// src/Lili/PariBundle/Controller/PariController.php

namespace Lili\PariBundle\Controller;

use Lili\PariBundle\Entity\Pari;
use Lili\PariBundle\Form\PariType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PariController extends Controller
{
    /**
     * @param $page
     * @return Response
     */
    public function listeAction($page)
    {
        if($page===0) {
            $response = new Response;
            $response->setContent("Ceci est une page d'erreur 404 : la page '0' n'existe pas !");
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }

        $content = $this->renderView('LiliPariBundle:Pari:liste.html.twig',
            array()
        );
        return new Response($content);
    }

    public function voirAction($id)
    {
        $content = $this->renderView('LiliPariBundle:Pari:voir.html.twig',
            array('id' => $id)
        );
        return new Response($content);
    }

    public function creerAction(Request $request)
    {
        $pari = new Pari();

        $form = $this->get('form.factory')->create(new PariType(), $pari);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pari);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'Pari bien enregistré.');

            return $this->redirect($this->generateUrl('lili_pari_creer'));
        }

        return $this->render('LiliPariBundle:Pari:creer.html.twig', array(
            'form' => $form->createView(),
            'titre' => 'Créer un nouveau pari',
            'texte_bouton' => 'Créer le pari'
        ));
    }


    public function modifierAction(Request $request, $id)
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LiliPariBundle:Pari')
        ;
        $nouveau=true;
        $pari = $repository->find($id);
        if($pari===null) {
            if($id!=0)
                $request->getSession()->getFlashBag()->add('warning', 'Le pari d\'id '.$id.' n\'existe pas. Vous pouvez créer un nouveau pari à la place.');
            $pari = new Pari();
        }else{
            $nouveau=false;
        }


        $form = $this->get('form.factory')->create(new PariType(), $pari);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pari);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'Pari bien enregistré.');

            return $this->redirect($this->generateUrl('lili_pari_creer'));
        }

        return $this->render('LiliPariBundle:Pari:creer.html.twig', array(
            'form' => $form->createView(),
            'titre' => ($nouveau ? 'Créer un nouveau pari' : 'Modifier le pari \''.$pari->getTitre() ).'\'',
            'texte_bouton' => ($nouveau ? 'Créer le pari' : 'Modifier le pari')
        ));
    }



    public function resultatAction($id)
    {
        $content = $this->renderView('LiliPariBundle:Pari:resultat.html.twig',
            array('id' => $id)
        );
        return new Response($content);
    }

    public function supprimerAction($id)
    {
        $content = $this->renderView('LiliPariBundle:Pari:supprimer.html.twig',
            array('id' => $id)
        );
        return new Response($content);
    }

    public function menuAction()
    {
        $user = $this->getUser();
        $args = array(
            'solde' => $user->getSolde(),
            'pariEnCours' => count($user),
            'pariTermine' => 'par exemple 3'
        );

        return $this->render( 'LiliPariBundle:Pari:menu.html.twig', $args );
    }

}