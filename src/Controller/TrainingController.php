<?php

namespace App\Controller;

use App\Repository\StagiaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrainingController extends AbstractController
{
    /**
     * @Route("/stagiaire/list", name="stagiaire_list")
     */
    public function listStagiaires(StagiaireRepository $repo): Response
    {
        return $this->render('training/stagiaires/index.html.twig', [
            'stagiaires' => $repo->findAll()
        ]);
    }
}
