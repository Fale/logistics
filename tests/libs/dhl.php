<?php
require( "src/libs/dhl.php" );
class Dhl extends PHPUnit_Framework_TestCase
{
    public function testQuote()
    {
        $data['debug'] = 1;

        $data['Username'] = "radius";
        $data['Password'] = "c3Rm3ll!-";

        $data['OriginPersonName'] = "Ferdinando Cermelli";
        $data['OriginCompanyName'] = "Radius di Ferdinando Cermelli";
        $data['OriginPhoneNumber'] = "+393482668888";
        $data['OriginEmailAddress'] = "f.cermelli@snds.co";

        $data['OrigStreetLine'] = "Via Tolstoj, 86";
        $data['OrigCountryId'] = "IT";
        $data['OrigPostal'] = "20098";
        $data['OrigCity'] = "San Giuliano Milanese";

        $data['OrigStreetLine'] = "Kemp House, 152-160 City Rd";
        $data['OrigCountryId'] = "GB";
        $data['OrigPostal'] = "EC1V 2NX";
        $data['OrigCity'] = "London";

        $data['DestPersonName'] = "Fabio Locati";
        $data['DestCompanyName'] = "Ship and Sale Ltd";
        $data['DestPhoneNumber'] = "+393482668888";
        $data['DestEmailAddress'] = "f.cermelli@snds.co";

        $data['DestStreetLine'] = "Via Olmo, 80";
        $data['DestCountryId'] = "IT";
        $data['DestPostal'] = "20090";
        $data['DestCity'] = "Segrate";
        $data['StateOrProvinceCode'] = "MI";
/*
        $data['DestStreetLine'] = "Kemp House, 152-160 City Rd";
        $data['DestCountryId'] = "GB";
        $data['DestPostal'] = "EC1V 2NX";
        $data['DestCity'] = "London";
        $data['StateOrProvinceCode'] = "Greater London";
 */
        $data['ShipTimeStamp'] = "2012-05-15T12:00:00GMT+01:00";
        $data['Documents'] = "0";
        //$data['AccountID'] = "128948309";
        $data['AccountID'] = "105891642";
        $data['CustomerReferences'] = "3-2012-1712";
        $data['Packages']['1'] = Array( 'Weight' => '1.0', 'Length' => '21', 'Width' => '30', 'Height' => '6' );

        print_r( $data );
        $dhl = new Dhlws( $data );
        $d = $dhl->quote();
        print_r( $d ); 
        //print_r( $dhl->shipment() ); */
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
