<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Formation;
use App\Form\SessionType;
use App\Form\FormationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CatalogController extends AbstractController
{
    /**
     * @Route("/formation/list", name="formation_list")
     */
    public function listFormations(){

        $formations = $this->getDoctrine()->getRepository(Formation::class)->findAll();

        $listformations = [];
        
        foreach($formations as $formation){
             
            $programmeComplet = [];
            
            foreach($formation->getProgrammes() as $prog){
                $cat = $prog->getModule()->getCategory()->getTitle();
                if(!array_key_exists($cat, $programmeComplet)){
                    $programmeComplet[$cat] = [];
                }
                $programmeComplet[$cat][] = $prog;
            }
            $listformations[] = ["formation" => $formation, "programme" => $programmeComplet];
        }
        
        return $this->render("catalog/formation/list.html.twig", [
            "listformations" => $listformations
        ]);
    }

    /**
     * @Route("/formation/show/{id}", name="formation_show")
     */
    public function showFormation(Formation $formation){

        $programmes = $formation->getProgrammes();

        $programmeComplet = [];

        foreach($programmes as $prog){
            $cat = $prog->getModule()->getCategory()->getTitle();
            if(!array_key_exists($cat, $programmeComplet)){
                $programmeComplet[$cat] = [];
            }
            $programmeComplet[$cat][] = $prog;
        }

        return $this->render("catalog/formation/show.html.twig", [
            "formation" => $formation,
            "programme" => $programmeComplet
        ]);
    }

    /**
     * @Route("/formation/add", name="formation_add")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addFormation(Request $request): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($formation);
            $em->flush();

            return $this->redirectToRoute("formation_list");
        }

        return $this->render('catalog/formation/add.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/session/add", name="session_add")
     * @IsGranted("ROLE_SECRETARY")
     */
    public function addSession(Request $request): Response
    {
        
        $session = new Session();

        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($session);
            $em->flush();

            return $this->redirectToRoute("dashboard");
        }

        return $this->render('catalog/session/add.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
