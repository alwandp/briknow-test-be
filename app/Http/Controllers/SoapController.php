<?php

namespace App\Http\Controllers;

use Artisaninweb\SoapWrapper\SoapWrapper;
use App\Soap\Request\GetConversionAmount;
use App\Soap\Response\GetConversionAmountResponse;

class SoapController
{
    /**
     * @var SoapWrapper
     */
    protected $soapWrapper;

    /**
     * SoapController constructor.
     *
     * @param SoapWrapper $soapWrapper
     */
    public function __construct(SoapWrapper $soapWrapper)
    {
        $this->soapWrapper = $soapWrapper;
    }

    /**
     * Use the SoapWrapper
     */
    public function show() 
    { 
        //INI BELUM DI TEST DAN BELUM TAU SYNTAXNYA BENER ATAU NGGA KARNA GA ADA WSDLNYA BUAT DI TEST
        $this->soapWrapper->add('validate_adUser', function ($service) {
        $service
            ->wsdl('http://DESKTOP-2JEKBMC:9999/mockndfdXMLBinding')
            ->trace(true);
        });

        // Without classmap
        $response = $this->soapWrapper->call('validate_adUser', [
            'ldap_user' => 'username', 
            'ldap_pass'   => 'password',
        ]);

        dd($response);
        exit;
    }
}
