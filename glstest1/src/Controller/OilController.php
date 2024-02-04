<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\PrezzoOlio\Utils\OilHelper;
class OilController extends AbstractController{

 protected $catalogoOlio;
    public function __construct(OilHelper $catalogoOlio){
        $this->catalogoOlio = $catalogoOlio;
    }

    #[Route('/oil/storico',name: 'oil_storico')]
    public function storico(): Response{
        $storico= $this->catalogoOlio->getPrezziOlioList();
        $final_html= $this->catalogoOlio->printData($storico);
        return new Response($final_html);
    }

    #[Route('/oil/storico-json',name: 'oil_storico_json')]
    public function storicoJson(): Response{
        $storico= $this->catalogoOlio->getStoricoOlio();
        $final_html= $this->catalogoOlio->printJson($storico);
        return new Response($final_html);
    }

    #[Route('/oil/rpc', name:"oil_rpc", methods: ['POST'])]
    public function GetOilPriceTrend(Request $request): JsonResponse{
        $requestData = json_decode($request->getContent(), true);
        if ($requestData['method'] === 'GetOilPriceTrend') {
            $startDate = $requestData['params']['startDateISO8601'];
            $endDate = $requestData['params']['endDateISO8601'];
            $prices = $this->catalogoOlio->getOilPrices($startDate, $endDate);
            $response = [
                'jsonrpc' => '2.0',
                'id' => $requestData['id'],
                'result' => ['prices' => $prices]
            ];
            return new JsonResponse($response);
        }
        return new JsonResponse(['error' => 'Method not found'], JsonResponse::HTTP_NOT_FOUND);
    }
}
