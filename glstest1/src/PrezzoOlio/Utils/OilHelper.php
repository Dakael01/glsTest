<?php

namespace App\PrezzoOlio\Utils;
use Symfony\Contracts\Cache\CacheInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PrezziOlio;

class OilHelper{

    protected $cache;
    protected $entityManager;
    public function __construct(CacheInterface $cache, EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    const URL_STORICO_OLIO="https://pkgstore.datahub.io/core/oil-prices/brent-day_json/data/b26a150f66f90717c7e533ecc468baef/brent-day_json.json";


    /// questa funzione prende la source del json e la salva in cache. questo perchè, volendo, nel caso di poche chiamate su questo dato potrei addiruttra
    /// valutare di tenere solo questo json in cache e non salvarlo in tabella. facendo direttamente i controlli su di lui. perderei in performance ma
    /// guadagnerei in spazio e operazioni fatte sul db. Valutando però il caso, non mi pare questa la casistica.
    public function getStoricoOlio(){
        $cacheKey = 'prezzo_olio';
        $data = $this->cache->get($cacheKey, function($item) {
            $data = $this->getJsonFileInfo();
            $item->expiresAfter(5 * 3600);
            $item->set($data);
        });
        return $data;
    }


    public function getJsonFileInfo(){
        $json = file_get_contents(self::URL_STORICO_OLIO);
        $data = json_decode($json, true);
        return $data;
    }

    public function getPrezziOlioList(){
        $list=$this->entityManager->getRepository(PrezziOlio::class)->findAll();
       if(empty($list)){
            $this->riempiTabellaPrezziOlio();
            $list=$this->entityManager->getRepository(PrezziOlio::class)->findAll();
        }
        return $list;
    }

    // in questa funzione salvo direttamente gli storici nella tabella, con logica in update
    public function riempiTabellaPrezziOlio():void{
        $storico_olio = $this->getStoricoOlio();
        $last = $this->entityManager->getRepository(PrezziOlio::class)->findOneBy([], ['data' => 'DESC']);
        $itemsToInsert = [];
        if (isset($last) && !empty($last)) {
            $last_date = \DateTime::createFromFormat('Y-m-d', $last->getData());
        }
        foreach ($storico_olio as $value) {
            $item_date = \DateTime::createFromFormat('Y-m-d', $value["Date"]);
            if (isset($last_date) && $item_date <= $last_date) {
                continue;
            }
            $itemsToInsert[] = ['data' => $item_date, 'prezzo' => $value["Brent Spot Price"]];
        }
        /// inserimento dei dati in batch da 200
        $batchSize = 1000;
        $count = 0;
        foreach ($itemsToInsert as $item) {
            $prezzo_olio = new PrezziOlio();
            $prezzo_olio->setData($item['data']);
            $prezzo_olio->setPrezzo($item['prezzo']);
            $this->entityManager->persist($prezzo_olio);
            if (++$count % $batchSize === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }
        $this->entityManager->flush();
    }

    protected function creaNuovoPrezzoOlio($date, $brentSpotPrice):void{
        $prezzo_olio=new PrezziOlio();
        $prezzo_olio->setData($date);
        $prezzo_olio->setPrezzo($brentSpotPrice);
        $this->entityManager->persist($prezzo_olio);
        $this->entityManager->flush();
    }


    public function getOilPrices( $startDate, $endDate): array{
        $startDate_formattata = \DateTime::createFromFormat('Y-m-d', $startDate);
        $endDate_formattata = \DateTime::createFromFormat('Y-m-d', $endDate);
        $storico_olio=$this->getStoricoOlio();
        $return_array=[];
        foreach ($storico_olio as $value) {
            $value_date_formattata= \DateTime::createFromFormat('Y-m-d', $value["Date"]);
            if($value_date_formattata>=$startDate_formattata && $value_date_formattata<=$endDate_formattata){
                $return_array[]=array(
                    "dateISO8601"=>$value["Date"],
                    "Price"=>$value["Brent Spot Price"]
                );
            }
        }
        return $return_array;
    }

    public function printJson($json_data):string{

        $final_html= '<div><h1>LISTA JSON</h1>
            <style>
            .lista-olio {
                list-style-type: none;
                padding: 0;
            }
            .lista-olio li {
                padding: 10px;
                margin-bottom: 5px;
            }
            .lista-olio li:nth-child(even) {
                background-color: #d5d5d5;
            }
            .lista-olio li:nth-child(odd) {
                background-color: #a7ea86;
            }
        </style></head><body><div>';
        $final_html .= '<ul class="lista-olio">';
        foreach ($json_data as $value) {
            $final_html .= '<li>';
            $final_html .= '<strong>Brent Spot Price:</strong> ' . $value["Brent Spot Price"] . '<br>';
            $final_html .= '<strong>Date:</strong> ' . $value["Date"] . '<br>';
            $final_html .= '</li>';
            $final_html .= '<br>';
        }
        $final_html .= '</ul></div>';
        return $final_html;
    }


    public function printData($item_list):string{
        $final_html= '<div><h1>LISTA JSON</h1>
            <style>
            .lista-olio {
                list-style-type: none;
                padding: 0;
            }
            .lista-olio li {
                padding: 10px;
                margin-bottom: 5px;
            }
            .lista-olio li:nth-child(even) {
                background-color: #d5d5d5;
            }
            .lista-olio li:nth-child(odd) {
                background-color: #a7ea86;
            }
        </style></head><body><div>';
        $final_html .= '<ul class="lista-olio">';
        foreach ($item_list as $item) {
            $final_html .= '<li>';
            $final_html .= '<strong>Brent Spot Price:</strong> ' . $item->getPrezzo(). '<br>';
            $final_html .= '<strong>Date:</strong> ' . $item->getData() . '<br>';
            $final_html .= '</li>';
            $final_html .= '<br>';
        }
        $final_html .= '</ul></div>';
        return $final_html;
    }

}
