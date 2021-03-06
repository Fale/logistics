<?php
require( "src/libs/dhl.php" );
class Dhl extends PHPUnit_Framework_TestCase
{
    public $credentials = Array( 'Username' => 'radius', 
                                 'Password' => 'c3Rm3ll!-' );
    public $originData = Array( 'OriginPersonName' => 'Ferdinando Cermelli',
                                'OriginCompanyName' => 'Radius di Ferdinando Cermelli',
                                'OriginPhoneNumber' => '+393482668888',
                                'OriginEmailAddress' => 'f.cermelli@snds.co' );
    public $originItaly = Array( 'OrigStreetLine' => 'Via Tolstoj, 86',
                                 'OrigCountryId' => 'IT',
                                 'OrigPostal' => '20098',
                                 'OrigCity' => 'San Giuliano Milanese' );
    public $originUK = Array( 'OrigStreetLine' => 'Kemp House, 152-160 City Rd',
                              'OrigCountryId' => 'GB',
                              'OrigPostal' => 'EC1V 2NX',
                              'OrigCity' => 'London' );
    public $destinationData = Array( 'DestPersonName' => 'Fabio Locati',
                                     'DestCompanyName' => 'Ship and Sale Ltd',
                                     'DestPhoneNumber' => '+393482668888',
                                     'DestEmailAddress' => 'f.cermelli@snds.co' );
    public $destinationItaly = Array( 'DestStreetLine' => 'Via Olmo, 80',
                                      'DestCountryId' => 'IT',
                                      'DestPostal' => '20090',
                                      'DestCity' => 'Segrate',
                                      'StateOrProvinceCode' => 'MI' );
    public $destinationUK = Array( 'DestStreetLine' => 'Kemp House, 152-160 City Rd',
                                   'DestCountryId' => 'GB',
                                   'DestPostal' => 'EC1V 2NX',
                                   'DestCity' => 'London',
                                   'StateOrProvinceCode' => 'Greater London' );
    public $packagesLite = Array( 'Weight' => '1.0',
                                  'Length' => '21',
                                  'Width' => '30',
                                  'Height' => '6' );
    public $genericData = Array( 'ShipTimeStamp' => '2012-05-15T12:00:00GMT+01:00',
                                 'Documents' => '0',
                                 'CustomerReferences' => '3-2012-1712' );
    public $genericDataShip = Array( 'ShipTimeStamp' => '2012-06-05T12:00:00GMT+01:00',
                                 'Documents' => '0',
                                 'CustomerReferences' => '3-2012-1712' );
    public $accountIDCamion = '771050622';
    public $accountIDExpress = '105891642';

    public function testQuoteIt2It()
    {
        $data = Array();
        $data['Debug'] = 1;
        $data['AccountID'] = $this->accountIDExpress;
        $data = array_merge( $data, $this->credentials );
        $data = array_merge( $data, $this->originData );
        $data = array_merge( $data, $this->destinationData );
        $data = array_merge( $data, $this->originItaly );
        $data = array_merge( $data, $this->destinationItaly );
        $data['Packages']['1'] = $this->packagesLite;
        $data = array_merge( $data, $this->genericData );
        $dhl = new Dhlws( $data );
        $d = $dhl->quote();
        print_r( $d );
        $this->assertNotEmpty( $d );
    }
 
    public function testShipIt2It()
    {
        $data = Array();
        $data['Debug'] = 1;
        $data['AccountID'] = $this->accountIDExpress;
        $data = array_merge( $data, $this->credentials );
        $data = array_merge( $data, $this->originData );
        $data = array_merge( $data, $this->destinationData );
        $data = array_merge( $data, $this->originItaly );
        $data = array_merge( $data, $this->destinationItaly );
        $data['service'] = "A";
        $data['Packages']['1'] = $this->packagesLite;
        $data = array_merge( $data, $this->genericDataShip );
        $dhl = new Dhlws( $data );
        $d = $dhl->shipment();
        print_r( $d );
        $this->assertNotEmpty( $d );
    }
 
    public function testQuoteIt2Uk()
    {
        $data = Array();
        $data['Debug'] = 1;
        $data['AccountID'] = $this->accountIDExpress;
        $data = array_merge( $data, $this->credentials );
        $data = array_merge( $data, $this->originData );
        $data = array_merge( $data, $this->destinationData );
        $data = array_merge( $data, $this->originItaly );
        $data = array_merge( $data, $this->destinationUK );
        $data['Packages']['1'] = $this->packagesLite;
        $data = array_merge( $data, $this->genericData );
        $dhl = new Dhlws( $data );
        $d = $dhl->quote();
        print_r( $d );
        $this->assertNotEmpty( $d );
    }

    public function testShipIt2Uk()
    {
        $data = Array();
        $data['Debug'] = 1;
        $data['AccountID'] = $this->accountIDExpress;
        $data = array_merge( $data, $this->credentials );
        $data = array_merge( $data, $this->originData );
        $data = array_merge( $data, $this->destinationData );
        $data = array_merge( $data, $this->originItaly );
        $data = array_merge( $data, $this->destinationUK );
        $data['service'] = "K";
        $data['Packages']['1'] = $this->packagesLite;
        $data = array_merge( $data, $this->genericData );
        $dhl = new Dhlws( $data );
        $d = $dhl->shipment();
        print_r( $d );
        $this->assertNotEmpty( $d );
    }

    public function testQuoteIt2UkCamion()
    {
        $data = Array();
        $data['Debug'] = 1;
        $data['AccountID'] = $this->accountIDCamion;
        $data = array_merge( $data, $this->credentials );
        $data = array_merge( $data, $this->originData );
        $data = array_merge( $data, $this->destinationData );
        $data = array_merge( $data, $this->originItaly );
        $data = array_merge( $data, $this->destinationUK );
        $data['Packages']['1'] = $this->packagesLite;
        $data = array_merge( $data, $this->genericData );
        $dhl = new Dhlws( $data );
        $d = $dhl->quote();
        print_r( $d );
        $this->assertNotEmpty( $d );
    }
}
?>
