<?php
require( 'xmlstr_to_array.php' );

class Dhlws
{
    private $debug = 0;
    private $urlall = "https://wsbuat.dhl.com:8300/gbl/expressRateBook";
    private $data;

    public function Dhlws( $data )
    {
        $this->data = $data;
        if( array_key_exists( 'debug', $data ) )
            $this->debug = $data['debug'];
    }
    public function quote()
    {
        $xml = $this->requestRate( $this->data );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->urlall);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('SOAPAction: "euExpressRateBook_providerServices_ShipmentHandlingServices_Binder_getRateRequest"'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        $output = curl_exec($ch);
        if( $this->debug )
            print_r ( $xml->asXML() );
        return $this->parseRequestRate( $output );
    }

    public function shipment()
    {
        $xml = $this->requestShipment( $this->data );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->urlall);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('SOAPAction: "euExpressRateBook_providerServices_ShipmentHandlingServices_Binder_getRateRequest"'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        $output = curl_exec($ch);
        $output = curl_exec($ch);
        if( $this->debug )
            print_r ( $xml->asXML() );
        return $output;
    }

    private function soapHeader()
    {
        $xmlStr = '<?xml version = "1.0" encoding = "UTF-8"?><soapenv:Envelope xmlns:rat="http://scxgxtt.phx-dc.dhl.com/euExpressRateBook/RateMsgRequest" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"/>';
        $xml = new SimpleXMLElement($xmlStr);

        /// SoapEnv:Header
        $soapHeader = $xml->addChild('soapenv:Header');
        $wsseSecurity = $soapHeader->addChild('wsse:Security', '', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
        $wsseSecurity->addAttribute('soap:mustUnderstand', '1', 'http://schemas.xmlsoap.org/soap/envelope/');
        $wsseUsernameToken = $wsseSecurity->addChild('wsse:UsernameToken');
        $wsseUsernameToken->addAttribute('wsu:Id', 'UsernameToken-1', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd' );
        $wsseUsernameToken->addChild('wsse:Username', $this->data['Username']);
        $wssePassword = $wsseUsernameToken->addChild('wsse:Password', $this->data['Password']);
        $wssePassword->addAttribute('Type', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText');
        $wsseUsernameToken->addChild('wsu:Created', date('Y-m-d\TH:i:s.B\Z'));
        $wsseNonce = $wsseUsernameToken->addChild( 'wsse:Nonce', 'eUYebYfsjztETJ4Urt8AJw==' );
        $wsseNonce->addAttribute('EncodingType', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary');
        return $xml;
    }

    private function ship( $nodeRequestedShipment, $ship = 0){
        $nodeShip = $nodeRequestedShipment->addChild('Ship');

        if( $ship )
        {
            $nodeShipper = $nodeShip->addChild('Shipper');
            $nodeShipperContact = $nodeShipper->addChild('Contact');
            $nodeShipperContact->addChild('PersonName', $this->data['OriginPersonName']);
            $nodeShipperContact->addChild('CompanyName', $this->data['OriginCompanyName']);
            $nodeShipperContact->addChild('PhoneNumber', $this->data['OriginPhoneNumber']);
            $nodeShipperContact->addChild('EmailAddress', $this->data['OriginEmailAddress']);
            $nodeShipperAddress = $nodeShipper->addChild('Address');
        }
        else
            $nodeShipperAddress = $nodeShip->addChild('Shipper');
        $nodeShipperAddress->addChild('StreetLines', $this->data['OrigStreetLine']);
        $nodeShipperAddress->addChild('City', $this->data['OrigCity']);
        $nodeShipperAddress->addChild('PostalCode', $this->data['OrigPostal']);
        $nodeShipperAddress->addChild('CountryCode', $this->data['OrigCountryId']);

        if( $ship )
        {
            $nodeRecipient = $nodeShip->addChild('Recipient');
            $nodeRecipientContact = $nodeRecipient->addChild('Contact');
            $nodeRecipientContact->addChild('PersonName', $this->data['DestPersonName']);
            $nodeRecipientContact->addChild('CompanyName', $this->data['DestCompanyName']);
            $nodeRecipientContact->addChild('PhoneNumber', $this->data['DestPhoneNumber']);
            $nodeRecipientContact->addChild('EmailAddress', $this->data['DestEmailAddress']);
            $nodeRecipientAddress = $nodeRecipient->addChild('Address');
        }
        else
            $nodeRecipientAddress = $nodeShip->addChild('Recipient');
        $nodeRecipientAddress->addChild('StreetLines', $this->data['DestStreetLine']);
        $nodeRecipientAddress->addChild('City', $this->data['DestCity']);
        $nodeRecipientAddress->addChild('PostalCode', $this->data['DestPostal']);
        //$nodeRecipient->addChild('StateOrProvinceCode', $data['StateOrProvinceCode']);
        $nodeRecipientAddress->addChild('CountryCode', $this->data['DestCountryId']);
    }

    private function packages( $nodeRequestedShipment, $ship = 0) {
        $nodePackages = $nodeRequestedShipment->addChild('Packages');

        foreach( $this->data['Packages'] as $id => $package )
        {
            $nodeRequestedPackages = $nodePackages->addChild('RequestedPackages');
            $nodeRequestedPackages->addAttribute('number', $id);
            if( $ship )
                $nodeRequestedPackages->addChild('Weight', $package['Weight']);
            else
            {
                $nodeWeight = $nodeRequestedPackages->addChild('Weight');
                $nodeWeight->addChild('Value', $package['Weight']);
            }
            $nodeDimensions = $nodeRequestedPackages->addChild('Dimensions');
            $nodeDimensions->addChild('Length', $package['Length']);
            $nodeDimensions->addChild('Width', $package['Width']);
            $nodeDimensions->addChild('Height', $package['Height']);
            if( $ship )
                $nodeRequestedPackages->addChild('CustomerReferences', $this->data['CustomerReferences']);
        }
    }

    private function requestRate() {
        $xml = $this->soapHeader();

        /// SoapEnv:Body
        $soapBody = $xml->addChild('soapenv:Body');
        $rateRequest = $soapBody->addChild('rat:RateRequest', '', 'http://scxgxtt.phx-dc.dhl.com/euExpressRateBook/RateMsgRequest');
        $rateRequest->addChild('ClientDetail', '', '');

        $nodeRequestedShipment = $rateRequest->addChild('RequestedShipment', '', '');
        $nodeRequestedShipment->addChild('DropOffType', 'REQUEST_COURIER');
        $this->ship( $nodeRequestedShipment );
        $this->packages( $nodeRequestedShipment );

        $nodeRequestedShipment->addChild('ShipTimestamp', $this->data['ShipTimeStamp']);
        $nodeRequestedShipment->addChild('UnitOfMeasurement', 'SI');
        if ( $this->data['Documents'] )
            $nodeRequestedShipment->addChild('Content', 'DOCUMENTS');
        else
            $nodeRequestedShipment->addChild('Content', 'NON_DOCUMENTS');
        $nodeRequestedShipment->addChild('Account', $this->data['AccountID']);
        $nodeBilling = $nodeRequestedShipment->addChild('Billing');
        $nodeBilling->addChild('ShipperAccountNumber', $this->data['AccountID']);
        $nodeBilling->addChild('ShippingPaymentType', 'S');
        $nodeBilling->addChild('BillingAccountNumber', $this->data['AccountID']);
        return $xml;
    }

    private function shipmentInfo( $nodeRequestedShipment )
    {
        $nodeShipmentInfo = $nodeRequestedShipment->addChild('ShipmentInfo');
        $nodeShipmentInfo->addChild('DropOffType', 'REQUEST_COURIER');
        $nodeShipmentInfo->addChild('ServiceType', 'K');
        $nodeShipmentInfo->addChild('Account', $this->data['AccountID']);
        $nodeShipmentInfo->addChild('Currency', 'EUR');
        $nodeShipmentInfo->addChild('UnitOfMeasurement', 'SI');
        $nodeShipmentInfo->addChild('ShipmentIdentificationNumber', 'SI');
    }

    private function internationalDetail( $nodeRequestedShipment )
    {
        $nodeInternationalDetail = $nodeRequestedShipment->addChild('InternationalDetail');
        $nodeCommodities = $nodeInternationalDetail->addChild('Commodities');
        $nodeCommodities->addChild('NumberOfPieces', '1'); // To make scalable
        $nodeCommodities->addChild('Description', 'DESC'); // To make scalable
        $nodeCommodities->addChild('CountryOfManufacture', 'IT'); // To make scalable
        $nodeCommodities->addChild('Quantity', '1'); // To make scalable
        $nodeCommodities->addChild('UnitPrice', '1'); // To make scalable
        $nodeCommodities->addChild('CustomsValue', '1'); // To make scalable
        if ( $this->data['Documents'] )
            $nodeInternationalDetail->addChild('Content', 'DOCUMENTS');
        else
            $nodeInternationalDetail->addChild('Content', 'NON_DOCUMENTS');
    }

    private function requestShipment()
    {
        $xml = $this->soapHeader();

        /// SoapEnv:Body
        $soapBody = $xml->addChild('soapenv:Body');
        $rateRequest = $soapBody->addChild('shipreq:ShipmentRequest', '', 'http://scxgxtt.phx-dc.dhl.com/euExpressRateBook/ShipmentMsgRequest');
        $nodeRequestedShipment = $rateRequest->addChild('RequestedShipment', '', '');
        $this->shipmentInfo( $nodeRequestedShipment, $this->data );
        $nodeRequestedShipment->addChild('ShipTimestamp', $this->data['ShipTimeStamp']);
        $nodeRequestedShipment->addChild('PaymentInfo', 'DAP');
        $this->internationalDetail( $nodeRequestedShipment, $this->data );
        $this->ship( $nodeRequestedShipment, $this->data, 1 );
        $this->packages( $nodeRequestedShipment, $this->data, 1 );

        return $xml;
    }

    private function parseRequestRate( $data )
    {
        if( $this->debug )
            print_r( $data );
        $output = Array();
        $array = xmlstr_to_array( $data );
        if( $this->debug )
            print_r( $array );
        if( ! array_key_exists( 'Service', $array['SOAP-ENV:Body']['rateresp:RateResponse']['Provider'] ) )
            return $array;
        $xml = $array['SOAP-ENV:Body']['rateresp:RateResponse']['Provider']['Service'];
        foreach( $xml as $id => $service )
        {
            if( $service['TotalNet']['Amount'] > "0.00" )
            {
                if( $service['Charges']['Charge'][0]['ChargeType'] )
                    $output[$id]['name'] = $service['Charges']['Charge'][0]['ChargeType'];
                else
                    $output[$id]['name'] = $service['Charges']['Charge']['ChargeType'];
                $output[$id]['price'] = $service['TotalNet']['Amount'];
                $output[$id]['delivery'] = $service['DeliveryTime'];
                $output[$id]['service'] = $service['@attributes']['type'];
            }
        }
        return $output;
    }
}

?>
