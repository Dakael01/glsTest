<?php

namespace App\PrezzoOlio\Utils;

class OilHelper{

    const URL_STORICO_OLIO="https://pkgstore.datahub.io/core/oil-prices/brent-day_json/data/b26a150f66f90717c7e533ecc468baef/brent-day_json.json";
    public function getStoricoOlio(){
        $json = file_get_contents(self::URL_STORICO_OLIO);
        $data = json_decode($json, true);
        return $data;
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

}
