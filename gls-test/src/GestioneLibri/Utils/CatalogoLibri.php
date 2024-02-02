<?php

namespace App\GestioneLibri\Utils;
use App\Entity\Libro;
use Doctrine\ORM\EntityManagerInterface;

class CatalogoLibri{

    protected $entityManager;
    protected $findVariable=array('titolo','autore','genere','anno','prezzo');
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    public function getLibri(){
        $libri = $this->entityManager->getRepository(Libro::class)->findAll();
        return $libri;
    }

    public function creaLibro($titolo, $autore,$genere,$anno,$prezzo){
        $libro = new Libro();
        $libro->setTitolo($titolo);
        $libro->setAutore($autore);
        $libro->setGenere($genere);
        $libro->setAnno($anno);
        $libro->setPrezzo($prezzo);
        $this->aggiungiLibro($libro);
        return $libro;
    }

    protected function aggiungiLibro($libro){
        $this->entityManager->persist($libro);
        $this->entityManager->flush();
    }

    public function cancellaLibroDaID($id){
        $libro = $this->entityManager->getRepository(Libro::class)->find($id);
        if ($libro) {
            $this->entityManager->remove($libro);
            $this->entityManager->flush();
        }
    }

    public function trovaLibriDaKeyword($parola_da_cercare,$keyword){
        if(in_array($keyword,$this->findVariable)!==false) {
            return $this->entityManager->getRepository(Libro::class)->findBy([$keyword => $parola_da_cercare]);
        }else{
            return null;
        }
    }

    ////// avendo la funzione trovaLibriDaKeyword, le due funzioni seguenti non sono più necessarie
    public function trovaLibroPerAutore($autore){
        return trovaLibriDaKeyword($autore, "autore");
    }

    public function trovaLibriPerGenere($genere){
        return trovaLibriDaKeyword($genere, "genere");
    }

    ///////////////////


    /*
     * utilizzo la funzione findBy e non FindOneBy perchè posso avere più libri con lo stesso titolo ma con edizioni stampate in anni diversi
     */
    public function rimuoviLibro($titolo){
        $libro_list = $this->entityManager->getRepository(Libro::class)->findBy(['titolo' => $titolo]);
        foreach($libro_list as $libro){
            $this->entityManager->remove($libro);
            $this->entityManager->flush();
        }
    }

    public function checkifBookExists($titolo, $autore, $genere, $anno, $prezzo){
        $libro = $this->entityManager->getRepository(Libro::class)->findOneBy([
            'titolo' => $titolo,
            'autore' => $autore,
            'genere' => $genere,
            'anno' => $anno,
            'prezzo' => $prezzo
        ]);
        if($libro){
            return true;
        }else{
            return false;
        }
    }


    /*
     * funzione per stampre in Frontend la lista dei libri in modo "decente"
     */
    public function printListaLibri($libri,$percentuale=null):string{
        $final_html= '<html><head><style>
            .lista-libri {
                list-style-type: none;
                padding: 0;
            }
            .lista-libri li {
                padding: 10px;
                margin-bottom: 5px;
            }
            .lista-libri li:nth-child(even) {
                background-color: #d5d5d5;
            }
            .lista-libri li:nth-child(odd) {
                background-color: #88ade0;
            }
        </style></head><body><div><h1>Lista libri</h1>';
        $final_html .= '<ul class="lista-libri">';
        foreach ($libri as $libro){
            $final_html .= '<li>';
            $final_html .= '<strong>Titolo:</strong> ' . $libro->getTitolo() . '<br>';
            $final_html .= '<strong>Autore:</strong><a href="/libri/autore/'.$libro->getAutore().'">' . $libro->getAutore() . '</a><br>';
            $final_html .= '<strong>Genere:</strong><a href="/libri/genere/'.$libro->getGenere().'"> ' . $libro->getGenere() . '</a><br>';
            $final_html .= '<strong>Anno:</strong> ' . $libro->getAnno() . '<br>';
            $final_html .= '<strong>Prezzo:</strong> ' . $libro->getPrezzo() . '<br>';
            if(!is_null($percentuale)){
                $final_html .= '<strong>Prezzo scontato:</strong> ' . $libro->calcolaSconto($percentuale) . '<br>';
            }
            $final_html .= '<a href="/libri/rimuovi/' . $libro->getId() . '">Rimuovi</a>';
            $final_html .= '</li>';
            $final_html .= '<br>';
        }
        $final_html.= '</ul>';
        $final_html.= "</body></html>";
        return $final_html;
    }

}
