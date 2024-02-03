<?php

namespace App\Command;

use App\Entity\PrezziOlio;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\PrezzoOlio\Utils\OilHelper;
#[AsCommand(
    name: 'importOilPrice',
    description: 'popolate oil_price table with data from json file'
)]
class ImportOilPriceCommand extends Command
{
    protected $entityManager;
    protected $OilHelper;
    public function __construct(EntityManagerInterface $entityManager, OilHelper $OilHelper){
        $this->OilHelper = $OilHelper;
        $this->entityManager = $entityManager;
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $json_data=$this->OilHelper->getJsonFileInfo();
        $last = $this->entityManager->getRepository(PrezziOlio::class)->findOneBy([], ['data' => 'DESC']);
        $itemsToInsert = [];
        if (isset($last) && !empty($last)) {
            $last_date = \DateTime::createFromFormat('Y-m-d', $last->getData());
            $output->writeln('Ultima data presente in tabella: ' . $last_date->format('Y-m-d'));
        }
        foreach ($json_data as $value) {
            $item_date = \DateTime::createFromFormat('Y-m-d', $value["Date"]);
            if (isset($last_date) && $item_date <= $last_date){
                continue;
            }
            $itemsToInsert[] = ['data' => $item_date, 'prezzo' => $value["Brent Spot Price"]];
        }
        if (empty($itemsToInsert)) {
            $output->writeln('Nessun nuovo dato da inserire');
            return Command::SUCCESS;
        }
        /// inserimento dei dati in batch da 200
        $batchSize = 200;
        $count = 0;
        $lastItem = end($itemsToInsert);
        $count_items = 0;
        foreach ($itemsToInsert as $item) {
            $output->writeln('Aggiungo in batch item con data' . $item['data']->format('Y-m-d') . ' e prezzo ' . $item['prezzo'] );
            $count_items++;
            $prezzo_olio = new PrezziOlio();
            $prezzo_olio->setData($item['data']->format('Y-m-d'));
            $prezzo_olio->setPrezzo($item['prezzo']);
            $this->entityManager->persist($prezzo_olio);
            if ((++$count % $batchSize === 0) || $item === $lastItem ) {
                $output->writeln("invio batch di $batchSize");
                $this->entityManager->flush();
                $this->entityManager->clear();
                $count = 0;
            }
        }
        $output->writeln("INSERITI $count_items");
        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}
