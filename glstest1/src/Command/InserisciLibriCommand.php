<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\GestioneLibri\Utils\CatalogoLibri;

#[AsCommand(name: 'inserisciLibri', description: 'Inserts test books into the database.',)]
class InserisciLibriCommand extends Command{

    protected $entityManager;
    protected $catalogoLibri;
    public function __construct(EntityManagerInterface $entityManager, CatalogoLibri $catalogoLibri){
        $this->catalogoLibri = $catalogoLibri;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(){
        $this->setDescription('Inserts test books into the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int{
        $libri_json = $this->getExampleJsonbookList();
        $libri_da_inserire = json_decode($libri_json, true);
        foreach ($libri_da_inserire as $libro_da_inserire) {
            $output->writeln('Aggiunta Libro: ' . $libro_da_inserire['titolo']);
            if($this->catalogoLibri->checkifBookExists($libro_da_inserire['titolo'], $libro_da_inserire['autore'], $libro_da_inserire['genere'], $libro_da_inserire['anno'], $libro_da_inserire['prezzo'])){
                $output->writeln( $libro_da_inserire['titolo'].' già presente: ');
            }else{
                $output->writeln('Libro non presente, lo aggiungo');
                $this->catalogoLibri->creaLibro($libro_da_inserire['titolo'], $libro_da_inserire['autore'], $libro_da_inserire['genere'], $libro_da_inserire['anno'], $libro_da_inserire['prezzo']);
            }
        }
        return Command::SUCCESS;
    }

    //////////////////////////////////////////////////////////////////////
    ///////////////// JSON di esempio per i libri  ///////////////////////
    //////////////////////////////////////////////////////////////////////
    public function getExampleJsonbookList()
    {
        return '[
    {
        "titolo": "Il signore degli anelli",
        "autore": "J.R.R. Tolkien",
        "genere": "Fantasy",
        "anno": "1954",
        "prezzo": 19.99
    },
    {
        "titolo": "1984",
        "autore": "George Orwell",
        "genere": "Distopia",
        "anno": "1949",
        "prezzo": 15.50
    },
    {
        "titolo": "Orgoglio e pregiudizio",
        "autore": "Jane Austen",
        "genere": "Romanzo",
        "anno": "1813",
        "prezzo": 12.99
    },
    {
        "titolo": "Il giovane Holden",
        "autore": "J.D. Salinger",
        "genere": "Romanzo",
        "anno": "1951",
        "prezzo": 14.75
    },
    {
        "titolo": "Cronache del ghiaccio e del fuoco",
        "autore": "George R.R. Martin",
        "genere": "Fantasy",
        "anno": "1996",
        "prezzo": 22.00
    },
    {
        "titolo": "Harry Potter e la pietra filosofale",
        "autore": "J.K. Rowling",
        "genere": "Fantasy",
        "anno": "1997",
        "prezzo": 18.25
    },
    {
        "titolo": "Le cronache di Narnia",
        "autore": "C.S. Lewis",
        "genere": "Fantasy",
        "anno": "1950",
        "prezzo": 16.99
    },
    {
        "titolo": "Il Piccolo Principe",
        "autore": "Antoine de Saint-Exupéry",
        "genere": "Romanzo",
        "anno": "1943",
        "prezzo": 13.50
    },
    {
        "titolo": "Anna Karenina",
        "autore": "Lev Tolstoj",
        "genere": "Romanzo",
        "anno": "1877",
        "prezzo": 16.99
    },
    {
        "titolo": "Lo Hobbit",
        "autore": "J.R.R. Tolkien",
        "genere": "Fantasy",
        "anno": "1937",
        "prezzo": 17.50
    },
    {
        "titolo": "Don Chisciotte della Mancia",
        "autore": "Miguel de Cervantes",
        "genere": "Romanzo",
        "anno": "1605",
        "prezzo": 14.99
    },
    {
        "titolo": "Moby Dick",
        "autore": "Herman Melville",
        "genere": "Romanzo",
        "anno": "1851",
        "prezzo": 15.99
    },
    {
        "titolo": "Il nome della rosa",
        "autore": "Umberto Eco",
        "genere": "Romanzo",
        "anno": "1980",
        "prezzo": 21.50
    },
    {
        "titolo": "Guerra e pace",
        "autore": "Lev Tolstoj",
        "genere": "Romanzo",
        "anno": "1869",
        "prezzo": 19.99
    },
    {
        "titolo": "Il vecchio e il mare",
        "autore": "Ernest Hemingway",
        "genere": "Romanzo",
        "anno": "1952",
        "prezzo": 14.25
    },
    {
        "titolo": "100 anni di solitudine",
        "autore": "Gabriel García Márquez",
        "genere": "Romanzo",
        "anno": "1967",
        "prezzo": 17.99
    },
    {
        "titolo": "Il processo",
        "autore": "Franz Kafka",
        "genere": "Romanzo",
        "anno": "1925",
        "prezzo": 16.50
    },
    {
        "titolo": "L\'isola del tesoro",
        "autore": "Robert Louis Stevenson",
        "genere": "Avventura",
        "anno": "1883",
        "prezzo": 13.99
    },
    {
        "titolo": "Il Grande Gatsby",
        "autore": "F. Scott Fitzgerald",
        "genere": "Romanzo",
        "anno": "1925",
        "prezzo": 18.99
    },
    {
        "titolo": "Il Conte di Montecristo",
        "autore": "Alexandre Dumas",
        "genere": "Romanzo",
        "anno": "1844",
        "prezzo": 19.25
    },
    {
        "titolo": "La fattoria degli animali",
        "autore": "George Orwell",
        "genere": "Satira",
        "anno": "1945",
        "prezzo": 14.99
    },
    {
        "titolo": "Il buio oltre la siepe",
        "autore": "Harper Lee",
        "genere": "Romanzo",
        "anno": "1960",
        "prezzo": 15.75
    },
    {
        "titolo": "Il mondo nuovo",
        "autore": "Aldous Huxley",
        "genere": "Distopia",
        "anno": "1932",
        "prezzo": 16.99
    },
    {
        "titolo": "Il Gobbo di Notre-Dame",
        "autore": "Victor Hugo",
        "genere": "Romanzo",
        "anno": "1831",
        "prezzo": 17.50
    },
    {
        "titolo": "Il giovane Werther",
        "autore": "Johann Wolfgang von Goethe",
        "genere": "Romanzo",
        "anno": "1774",
        "prezzo": 14.99
    }
]';
        }
}
