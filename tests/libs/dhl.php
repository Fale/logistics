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
                                 //'AccountID' => '771050622',
                                 'AccountID' => '105891642',
                                 'CustomerReferences' => '3-2012-1712' );
    public function testQuote()
    {
        $data = Array();
        $data = array_merge( $data, $this->credentials );
        $data = array_merge( $data, $this->originData );
        $data = array_merge( $data, $this->destinationData );
        $data = array_merge( $data, $this->originItaly );
        $data = array_merge( $data, $this->destinationItaly );
        $data['Packages'] = $this->packagesLite;
        $data = array_merge( $data, $this->genericData );

        $dhl = new Dhlws( $data );
        $d = $dhl->quote();
        $this->assertNotEmpty( $d );
    }
 
    public function testArrayContainsAnElement()
    {
        // Create the Array fixture.
        $fixture = array();
 
        // Add an element to the Array fixture.
        $fixture[] = 'Element';
 
        // Assert that the size of the Array fixture is 1.
        $this->assertEquals(1, sizeof($fixture));
    }
}
?>
