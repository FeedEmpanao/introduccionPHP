<?php

namespace App\Controller;

use App\Entity\Empresa;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmpresaController extends AbstractController
{
    #[Route('/empresa/{codigo}', name: 'app_empresa')]
    public function mostrar(ManagerRegistry $doctrine, $codigo): Response
    {
        $repositorio = $doctrine->getRepository(Empresa::class);
        $empresa  = $repositorio->find($codigo);

        return $this->render('ficha_empresa.html.twig', ['empresa' => $empresa]);
    }
    #[Route('/empresa/buscar/{texto}', name: 'buscar')]
    public function buscar(ManagerRegistry $doctrine, $texto): Response
    {
        $repositorio = $doctrine->getRepository(Empresa::class);
        $empresas  = $repositorio->findByNombre($texto);
        return $this->render('ficha_empresa.html.twig', ['empresa' => $empresas]);
    }

    #[Route('/empresa/nueva', name: 'nueva_empresa')]
    public function nueva(ManagerRegistry $doctrine, Request $request) {
        $empresa = new Empresa();
        $formulario = $this->createForm(EmpresaFormType::class, $empresa);
        $formulario->handleRequest($request);
        if ($formulario->isSubmitted() && $formulario->isValid()) {  
            $empresa = $formulario->getData(); 
            $entityManager = $doctrine->getManager();
            $entityManager->persist($empresa);
            $entityManager->flush();
            return $this->redirectToRoute('ficha_empresa', ["codigo" => $empresa->getId()]);
        }
        return $this->render('nueva.html.twig', array(
            'formulario' => $formulario->createView()
        ));
    }
}
