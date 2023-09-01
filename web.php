<?php

use Illuminate\Support\Facades\Route;
use Cielo\API30\Merchant;
use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\Sale;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Payment;
use Cielo\API30\Ecommerce\CreditCard;

use Cielo\API30\Ecommerce\Request\CieloRequestException;
use Illuminate\Support\Facades\Http;

use Cielo\Cielo;
use Cielo\CieloException;
use Cielo\Transaction;
use Cielo\Holder;
use Cielo\PaymentMethod;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/cielo-api', function(){

    $charSet = "UTF-8"; 
    $mediaType = "application/json";

   header("Content-Type: ".$mediaType);

    $dtAtual = Date('d/m/Y');   //  GERA A DATA ATUAL  //
    $hrAtual = Date('H:i:s');   //  GERA HORÁRIO ATUAL //
     
    // DEMEMBRANDO DATA PARA CAMPOS DA TABELA PODEREM PEGAR A DATA PARA GERAR CRÉDITO //
    $day   = substr($dtAtual,-10,2); // REMOVENDO O DIA DA DATA COMPLETA //
    $month = substr($dtAtual,-07,02);// REMOVENDO O MÊS DA DATA COMPLETA //
    $year  = substr($dtAtual,06);    // REMOVENDO O ANO DA DATA COMPLETA //

   // DEMEMBRANDO HORA PARA CAMPOS DA TABELA PODEREM PEGAR A DATA PARA GERAR CRÉDITO //
    $second = substr($hrAtual,-10,2); // REMOVENDO O DIA DA DATA COMPLETA //
    $minute = substr($hrAtual,-07,02);// REMOVENDO O MÊS DA DATA COMPLETA //
    $hour   = substr($hrAtual,06);    // REMOVENDO O ANO DA DATA COMPLETA //
    $ReceivedDate = $year.'-'.$month.'-'.$day; // MONTANDO DATA NO FORMADO DESEJADO //
    

    
    
    $ip = $_SERVER["REMOTE_ADDR"];   //  PEGA O NUMERO IP DO USÁRIO //


    // ESTAS VARIÁVEIS VEM DO ARQUIVO CREDITO.PHP //
    $code             = '123';    // RECEBE A VARIÁVEL DE CODIGO DO ATLETA //   
    $numero_cartao    = '4024.0071.5376.3198';  // TRAZ O NÚMERO DO CARTÃO //
    $nome_completo    = 'Arão Domingos';  // TRAZ O NOME QUE ESTÁ NO CARTÃO  //
    $validade         = '12/2030'; // TRAZ A VALIDADE DO CARTÃO //
    $bandeira         = 'Visa'; // TRAZ A BANDEIRA DO CARTÃO //
    $codigo_seguranca = '123'; // TRAZ O CÓDIGO DE SEGURANÇA DO CARTÃO //
    $valor_prova      = '15700'; // TRAZ O VALOR DA PROVA SELECIONADA //
    $codigo_inscr     = '15700'; // CÓDIGO DA INSCRIÇÃO //
    $param_pagto      = '15700'; // TRAZ O VALOR DO PARAMETRO PARA PAGAMENTO - SE ZERO VEM DE COMPRA, SE UM VEM DE PLANILHA OU TENTANDO REFAZER PAGAMENTO QUE NÃO DEU CERTO... NORMALMENTE COM CARTÃO MARTERCARD... ESTA ROTINA EXISTE PORQUE SENÃO AO TENTAR REFAZER O PAGAMENTO O SISTEMA GERA OUTRA INSCRIÇÃO PARA O ATLETA //
   //  FINAL DE COLEÇÃO DE VARIÁVEIS //

    //MONTANDO AQUI A ROTINA PARA JSON E ENVIO AO SERVER DA CIELO  ###  //
//  #####################################################################################################################     
     $MerchantOrderId = '2014111701';  //  NÚMERO DO PEDIDO //
     $MerchantId      = "8d7a0ae1-a76f-48bb-889c-fcce0ea9e449";  // MERCHANT ID PARA PRODUÇÃO //
     $MerchantKey     = "ZBFLXYWJKUQPMPZOYSYRACBXWMONWTUBHIAITNZJ"; // MERCHANT KEY PRODUÇÃO //
     $tipo            = "CreditCard"; // TIPO DE MEIO DE PAGAMENTO //
     $authenticate    = true; //  DEFINE SE O COMPRADOR SERÁ DIRECIONADO AO AMBIENTE DO BANCO OU NÃO - BOOLEANO //$url             = "https://api.cieloecommerce.cielo.com.br/1/sales/"; // URL DE PRODUÇÃO //


     $cardNumber     = $numero_cartao;    //  NÚMERO DO CARTÃO DO CLIENTE // 
     $holder         = $nome_completo;    //  NOME IMPRESSO NO CARTÃO //
     $expirationDate = $validade;         //  DATA DE VALIDADE IMPRESSO NO CARTÃO //
     $securityCode   = $codigo_seguranca; //  CÓDIGO DE SEGURANÇA //
     $brand          = $bandeira;         //  BANDEIRA DO CARTÃO //
     $insValor_fk    = str_replace('.','','1500');// TRANTANDO A VARIÁVEL COM O VALOR DO TÍTULO, REMOVENDO PONTOS     
     $url = "https://apisandbox.cieloecommerce.cielo.com.br/1/sales/";

// A PARTIR DESTA LINHA, CONSTRUI A COLEÇÃO DE VARIÁVEIS PARA MONTAR O ARQUIVO JSON //
    
    
    $Customer = array(
    "Name" => 'Arão Domingos'); //  NOME DO COMPRADOR //
    
    $CreditCard  = array( 
    "CardNumber" => $numero_cartao,
    "Holder" => $nome_completo,
    "ExpirationDate" => $validade,
    "SecurityCode" => $codigo_seguranca,
    "Brand" => $brand);


    $Payment = array(
    "Type" => $tipo,
    "Amount" => $insValor_fk,
    "Installments" => '1',
    "Provider" => "cielo",  // PRODUÇÃO
    "CreditCard" => $CreditCard);
//   FINAL DE COLEÇÃO DE VARIÁVEIS PARA ARQUIVO JSON //


    
//  COLOCA A COLEÇÃO JSON PARA MONTAR O OBJETO JSON
    $data_service_request = array(
    "MerchantId" => $MerchantId,
    "MerchantKey" => $MerchantKey,
    "MerchantOrderId" => $MerchantOrderId,
    "Customer" => $Customer,
    "Payment" => $Payment);


//  MONTANDO O OBJETO JSON //
    $data_post = json_encode($data_service_request);  


    $headers = array();
    $headers[] = "Content-Type: ".$mediaType;
    $headers[] = "MerchantId: ".$MerchantId;
    $headers[] = "MerchantKey: ".$MerchantKey;
  
  

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);   
    
   
// ESTA ROTINA ESTÁ SENDO MONTADA, POR QUE HORA O SERVIDOR DA CIELO ENVIA A RESPOSTA EM BRANCO, E A RESPOSTA EM BRANCO NÃO FUNCIONA NO JS E TRAVA A RESPOSTA //
    if(empty($result))
    {
                $Payment = array(
                                  "ReturnMessage" => 'problema',
                                  "Type" => $tipo,
                                  "Amount" => $insValor_fk,
                                  "Installments" => '1',
                                  "Provider" => "cielo",  //  AQUI PARA PRODUÇÃO //
                                  "CreditCard" => $CreditCard);
//   FINAL DE COLEÇÃO DE VARIÁVEIS PARA ARQUIVO JSON //
                $data_service_request = array(
                                               "MerchantId" => $MerchantId,
                                               "MerchantKey" => $MerchantKey,
                                               "MerchantOrderId" => $MerchantOrderId,
                                               "Customer" => $Customer,
                                               "Payment" => $Payment);


                //  MONTANDO O OBJETO JSON //
                $data_post = json_encode($data_service_request); 
                print  $data_post; 
    }

    else//  SE O SERVIDOR DA CIELO ENVIAR A RESPOSTA CORRETA, ENTRA NESTE BLOCO //
    {
         print $result;
    } 
    
});
Route::get('/teste', function(){
    // Configure o ambiente
$environment = $environment = Environment::sandbox();

// Configure seu merchant 
$merchant = new Merchant('3dcaa123-5861-4cef-bf8c-dfb897d8ea88DpHJY4TXKUCL', 'RTU8pAerdDUXPoLvnl2n­bQoSKpWOWBk2ccXUDpHJ');

// Crie uma instância de Sale informando o ID do pedido na loja
$sale = new Sale('123');

// Crie uma instância de Customer informando o nome do cliente
$customer = $sale->customer('Arão Domingos');

// Crie uma instância de Payment informando o valor do pagamento
$payment = $sale->payment(15700);

// Crie uma instância de Credit Card utilizando os dados de teste
// esses dados estão disponíveis no manual de integração
$payment->setType(Payment::PAYMENTTYPE_CREDITCARD)
        ->creditCard("123", CreditCard::VISA)
        ->setExpirationDate("12/2018")
        ->setCardNumber("0000000000000001")
        ->setHolder("Arão Domingos");


// Crie o pagamento na Cielo
try {
    // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
    $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);

    // Com a venda criada na Cielo, já temos o ID do pagamento, TID e demais
    // dados retornados pela Cielo
    $paymentId = $sale->getPayment()->getPaymentId();

    // Com o ID do pagamento, podemos fazer sua captura, se ela não tiver sido capturada ainda
    $sale = (new CieloEcommerce($merchant, $environment))->captureSale($paymentId, 15700, 0);

   
    // E também podemos fazer seu cancelamento, se for o caso
    $sale = (new CieloEcommerce($merchant, $environment))->cancelSale($paymentId, 15700);
} catch (CieloRequestException $e) {
    // Em caso de erros de integração, podemos tratar o erro aqui.
    // os códigos de erro estão todos disponíveis no manual de integração.
    $error = $e->getCieloError();
}
// ...
});

Route::group(['namespace' => 'Site'], function(){

    Route::get('/', 'SiteController@index')->name('site.index');
    Route::get('/bet', 'SiteController@match')->name('site.match');
    Route::get('/contact', 'SiteController@contacts')->name('site.contact');
    Route::get('/list-bet', 'SiteController@cartBet')->name('site.cartBet')->middleware('check.bet');
    Route::get('/payment', 'SiteController@checkout')->name('site.checkout');

});

Route::group(['namespace' => 'Bet'], function(){

    Route::post('/calculate-bet', 'BetController@calculateBet')->name('bet.calculate');
    Route::post('/bet', 'BetController@store')->name('bet.store');
    Route::delete('/delete-bet/{bet}', 'BetController@destroy')->name('bet.delete');
});


// Rotas para Autenticação

Route::group(['namespace' => 'Auth'], function(){

    // Login
    Route::get('/login', 'AuthController@pageLogin')->name('auth.pageLogin');
    Route::post('/login', 'AuthController@login')->name('auth.login');

    //Logout
    Route::post('/logout', 'AuthController@logout')->name('auth.logout');
    
    // Criar Conta
    Route::get('/register', 'AuthController@pageRegister')->name('auth.pageRegister');
    Route::post('/register', 'AuthController@register')->name('auth.register');
    
    Route::get('/recover-password', 'AuthController@pagePasswordReset')->name('auth.recoverPassword');
    Route::post('/recover-password', 'AuthController@passwordReset')->name('auth.recoverPassword');
    
    // Recuperar Senha
    Route::group(['middleware' => 'check.session_email'], function(){

        Route::get('/verification-code', 'AuthController@pageVerificationCode')->name('auth.pageVerificationCode');
        Route::post('/verification-code', 'AuthController@verificationCode')->name('auth.verificationCode');
        
        Route::get('/update-password', 'AuthController@pageUpdatePassword')->name('auth.pageUpdatePassword')->middleware('check.permission_update_password');
        Route::post('/update-password', 'AuthController@updatePassword')->name('auth.updatePassword')->middleware('check.permission_update_password');
    });

    

});

// Routas Para Admin

Route::group(['prefix' => 'admin', 'middleware' => 'check.admin'], function(){

    Route::group(['namespace' => 'Admin'], function(){
        Route::get('/dashboard', 'DashboardController@index')->name('admin.dashboard');
    });

    Route::group(['namespace' => 'Match'], function(){
        
        Route::post('/match', 'MatchController@store')->name('match.store');
    });
});

// Routas Para Usuario

Route::group(['prefix' => 'user', 'middleware' => 'check.user'], function(){


    Route::group(['namespace' => 'Payment'], function(){

        Route::get('/payments', 'PaymentController@index')->name('payment.index');
        Route::post('/payment', 'PaymentController@store')->name('payment.store');
    });


    Route::group(['namespace' => 'User'], function(){

        Route::get('/dashboard', 'DashboardController@index')->name('user.dashboard');
        Route::get('/profile', 'DashboardController@profile')->name('user.profile');
        Route::post('/update-profile/{user}', 'DashboardController@updateProfile')->name('user.updateProfile');
        Route::post('/update-password/{user}', 'DashboardController@updatePassword')->name('user.updatePassword');
    });

});

