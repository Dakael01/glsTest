<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\GestioneLibri\Utils\CatalogoLibri;

class LibriController extends AbstractController{

    protected $catalogoLibri;
    public function __construct(CatalogoLibri $catalogoLibri){
        $this->catalogoLibri = $catalogoLibri;
    }

    #[Route('/libri/lista',name: 'lista_libri')]
    public function lista(): Response{
        $libri= $this->catalogoLibri->getLibri();
        $final_html= $this->catalogoLibri->printListaLibri($libri);
        return new Response($final_html);
    }

    #[Route('/libri/genere/{genere}',name: 'genere_libro')]
    public function genere($genere): Response{
        $libri= $this->catalogoLibri->trovaLibriDaKeyword($genere,"genere");
        if ($libri==null){
            return new Response("Genere non trovato");
        }
        $final_html= $this->catalogoLibri->printListaLibri($libri);
        return new Response($final_html);
    }

    #[Route('/libri/autore/{autore}',name: 'autore_libro')]
    public function autore($autore): Response{
        $libri= $this->catalogoLibri->trovaLibriDaKeyword($autore,"autore");
        if ($libri==null){
            return new Response("Autore non trovato");
        }
        $final_html= $this->catalogoLibri->printListaLibri($libri);
        return new Response($final_html);
    }

    #[Route('/libri/sconto/{percentuale}',name: 'sconto_libro')]
    public function sconto($percentuale): Response{
        $libri= $this->catalogoLibri->getLibri();
        if ($libri==null){
            return new Response("Autore non trovato");
        }
        $final_html= $this->catalogoLibri->printListaLibri($libri,$percentuale);
        return new Response($final_html);
    }

    #[Route('/libri/rimuovi/{id}',name: 'rimuovi_libro')]
    public function rimuovi($id): Response{
        $this->catalogoLibri->cancellaLibroDaID($id);
        return $this->redirectToRoute('lista_libri');
    }

}
