<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Cryptocurrency;
use App\Entity\SauvegardeJournaliere;
use App\Entity\API;
use App\Form\CryptoType;
use App\Form\CryptoModificationType;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
class CryptoController extends AbstractController
{


    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil(): Response
    {

        $em = $this->getDoctrine()->getManager();

        $cryptoRepository = $em->getRepository(Cryptocurrency::class);

        $sauvegardeJournaliere = $em->getRepository(SauvegardeJournaliere::class);

        $listeCrypto = $cryptoRepository->findAll();

        $tableauNomCrypto = array();

        $aujourdhui = date('Y-m-d');
        foreach($listeCrypto as $nomCrypto){
            array_push($tableauNomCrypto, $nomCrypto->getName());
        }

        $stringCrypto = implode(",",$tableauNomCrypto);

        $resultAPI = $this->getAPICryptoInfo($stringCrypto);
        
        if(is_string($resultAPI) && strpos($resultAPI,'error ') !== false )
        { 

            $errorCode = str_replace('error ', '', $resultAPI);
            return $this->render('crypto/error_page.html.twig', ['message' => $this->getErrorMessageAPI($errorCode)]);
        }

        $valorisation = $this->calculValorisation($resultAPI);
        if($sauvegardeJournaliere->findByDate($aujourdhui) == null){

            $sauvegarde = new SauvegardeJournaliere();
            $sauvegarde->setDate($aujourdhui);
            $sauvegarde->setValorisationTotale(round($valorisation));
            $em->persist($sauvegarde);
            $em->flush();
        }
        return $this->render('crypto/accueil.html.twig', ['listeCrypto' => $listeCrypto, 'valorisation' => $valorisation, 'resultAPI' => $resultAPI]);
    }

    /**
     * La fonction calculValorisation vas servir a calculer la valorisation des cryptomonnaies présentes dans la base de donnée par rapport aux prix actuel récupérer par l'API en prenant en compte la quantité
     * La fonction reçoit le tableau contenant toutes les informations des cryptomonnaies
     */
    public function calculValorisation($tableauCrypto){
        $listeAPIPrice = array();
        $listeBDDPrice = array();
        $em = $this->getDoctrine()->getManager();
        $cryptoRepository = $em->getRepository(Cryptocurrency::class);
        $listeCrypto = $cryptoRepository->findAll();
        foreach($listeCrypto as $cryptoEnCours){
            array_push($listeBDDPrice, $cryptoEnCours->getTotalPrice());
            array_push($listeAPIPrice, $cryptoEnCours->getQuantity()*$tableauCrypto[$cryptoEnCours->getName()]['quote']['EUR']['price']);
        }
        $valorisation = array_sum($listeAPIPrice) - array_sum($listeBDDPrice);
        return round($valorisation);
    }

    /**
     * @Route("/ajout", name="ajout")
     * 
     */
    public function ajout(Request $request): Response
    {

        $crypto = new Cryptocurrency();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CryptoType::class, $crypto);
        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $quantity = $crypto->getQuantity();
                $price = $crypto->getPrice();
                $totalPrice = $quantity * $price;
                $crypto->setTotalPrice($totalPrice);
                $em->persist($crypto);
                $em->flush();
                return $this->redirectToRoute('accueil');
            }
        }
        return $this->render('crypto/ajout.html.twig', ['crypto' => $crypto, 'form' => $form->createView()]);
    }

    /**
     * @Route("/graph", name="graph")
     *   
     */
    public function graph(ChartBuilderInterface $chartBuilder): Response
    {
        $em = $this->getDoctrine()->getManager();
        $sauvegardeJournaliere = $em->getRepository(SauvegardeJournaliere::class);
        $listeSauvegarde = $sauvegardeJournaliere->findAll();
        $listeSauvegardeDates = array();
        $listeSauvegardeValorisation = array(); 

        foreach($listeSauvegarde as $laSauvegarde){
            array_push($listeSauvegardeDates, $laSauvegarde->getDate());
            array_push($listeSauvegardeValorisation, $laSauvegarde->getValorisationTotale());
        }
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $listeSauvegardeDates,
            'datasets' => [
                [
                    'label' => 'Valorisation',
                    'borderColor' => 'rgb(31,195,108)',
                    'data' => $listeSauvegardeValorisation,
                ],
            ],
        ]);
        $chart->setOptions([
            'scales' => [
                'yAxes' => [
                    ['ticks' => ['min' => min($listeSauvegardeValorisation), 'max' => max($listeSauvegardeValorisation)]],
                ],
            ],
        ]);

        return $this->render('crypto/chart.html.twig', [
            'chart' => $chart,
        ]);
    }
   
    /**
     * @Route("/suppression-montant/{id}", name="suppression-montant")
     * 
     */
    public function suppressionMontant(Request $request, int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $cryptoRepository = $em->getRepository(Cryptocurrency::class);
        $suppressionMontant = $cryptoRepository->find($id);

        if($suppressionMontant === null){
            $message = 'La crypto monnaie choisis n\'existe pas dans la base de données';
            return $this->render('crypto/error_page.html.twig', ['message' => $message]);
        }

        $currentQuantity = $suppressionMontant->getQuantity();
        $form = $this->createForm(CryptoModificationType::class, $suppressionMontant);
        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $cryptoSymbol = $suppressionMontant->getName();
                $quantity = $suppressionMontant->getQuantity();
                $newQuantity = $currentQuantity - $quantity;

                if($newQuantity <= 0){
                    $em->remove($suppressionMontant);
                    $em->flush();
                    $this->redirectToRoute('accueil');
                }
                $infoCrypto = $this->getAPICryptoInfo($cryptoSymbol);

                if(is_string($infoCrypto) && strpos($infoCrypto,'error ') !== false )
                { 
                    $errorCode = str_replace('error ', '', $infoCrypto);
                    return $this->render('crypto/error_page.html.twig', ['message' => $this->getErrorMessageAPI($errorCode)]);
                }
                
                $totalPrice = $newQuantity * $infoCrypto[$cryptoSymbol]['quote']['EUR']['price'];
                $suppressionMontant->setTotalPrice($totalPrice);
                $suppressionMontant->setQuantity($newQuantity);
                $em->persist($suppressionMontant);
                $em->flush();
                return $this->redirectToRoute('accueil');
            }
        }
        return $this->render('crypto/suppression.html.twig', ['form' => $form->createView()]);
    }

    public function getAPICryptoInfo($listeCrypto){
        $em = $this->getDoctrine()->getManager();
        $apiRepository = $em->getRepository(API::class);
        $getAPI = $apiRepository->findAll();
        setlocale(LC_MONETARY, 'fr_FR.UTF-8');
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest';
        $parameters = [
                'symbol' => $listeCrypto,
                'convert' => 'EUR'
            ];

        $headers = [
                'Accepts: application/json',
                'X-CMC_PRO_API_KEY: '.$getAPI[0]->getAPI(),
            ];
        $qs = http_build_query($parameters); 
        $request = "{$url}?{$qs}";
        $curl = curl_init(); 
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => $request,           
          CURLOPT_HTTPHEADER => $headers,    
          CURLOPT_RETURNTRANSFER => 1         
       ));
            
        $response = curl_exec($curl); 
        curl_close($curl); 

        $var = json_decode($response, true);

        if($var['status']['error_code'] !== 0){

            return 'error '.$var['status']['error_code'];
        }
        return $var['data'];
    }

    public function getErrorMessageAPI($code){
        switch($code){
            case '1001':
                return  "This API Key is invalid.";
                break;
            case '1002':
                return "API key missing.";
                break;
            case '1003':
                return "Your API Key must be activated. Please go to pro.coinmarketcap.com/account/plan.";
                break;
            case '1004':
                return "Your API Key's subscription plan has expired.";
                break;
            case '1005':
                return "An API Key is required for this call.";
                break;
            case '1006':
                return "Your API Key subscription plan doesn't support this endpoint.";
                break;
            case '1007':
                return "This API Key has been disabled. Please contact support.";
                break;
            case '1008':
                return "You've exceeded your API Key's HTTP request rate limit. Rate limits reset every minute.";
                break;
            case '1009':
                return "You've exceeded your API Key's daily rate limit.";
                break;
            case '1010':
                return "You've exceeded your API Key's monthly rate limit.";
                break;
            case '1011':
                return "You've hit an IP rate limit.";
                break;
        }
    }

    /**
     * @Route("/", name="home")
     */
    public function home(){
        return $this->redirectToRoute('accueil');
    }
}
