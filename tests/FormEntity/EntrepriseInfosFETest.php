<?php

namespace Celtic34fr\ContactCore\Tests\FormEntity;

use Celtic34fr\ContactCore\FormEntity\EntrepriseInfosFE;
use PHPUnit\Framework\TestCase;

class EntrepriseInfosFETest extends TestCase
{
    private array $badInfos = [];
    private array $goodInfos = [];
    private EntrepriseInfosFE $infos; 

    public function setUp(): void
    {
        $this->badInfos = [
            'designation' => 'ceci est un test',
            'CA' => 34500,
            'telephone' => '0401020304',
            'siret' => '0123456780123',
        ];
        $this->goodInfos = [
            'designation' => 'une désignation',
            'siren' => '012345678',
            'siret' => '0123456780123',
            'courriel' => 'courriel@net.net',
            'telephone' => '0401020304',
            'reponse' => '',
            'logoID' => 45,
        ];
    }

    public function testCreateObject()
    {
        $info1 = new EntrepriseInfosFE($this->badInfos);
        $info2 = new EntrepriseInfosFE();

        $array1 = $info1->getInfosArray();
        $array2 = $info2->getInfosArray();
        
        /** validation array1 contains */
        $this->assertArrayNotHasKey('CA', $array1);
        $this->assertArrayNotHasKey('courriel', $array1);
        $this->assertArrayNotHasKey('reponse', $array1);
        $this->assertArrayNotHasKey('logoµID', $array1);
        $this->assertArrayHasKey('designation', $array1);
        $this->assertArrayHasKey('siren', $array1);
        $this->assertArrayHasKey('siret', $array1);
        $this->assertArrayHasKey('telephone', $array1);
        $this->assertNotEquals($info1, $array1);

        /** validation array2 empty values */
        $this->assertArrayNotHasKey('courriel', $array2);
        $this->assertArrayNotHasKey('reponse', $array2);
        $this->assertArrayNotHasKey('logoµID', $array2);
        $this->assertArrayNotHasKey('designation', $array2);
        $this->assertArrayNotHasKey('siren', $array2);
        $this->assertArrayNotHasKey('siret', $array2);
        $this->assertArrayNotHasKey('telephone', $array2);

        /** validation affectation goodInfos to info2 */
        $info2->setByArray($this->goodInfos);
        $array2 = $info2->getInfosArray();
        $this->assertEquals($info2->getDesignation(), $array2['designation']);
        $this->assertEquals($info2->getSiren(), $array2['siren']);
        $this->assertEquals($info2->getSiret(), $array2['siret']);
        $this->assertEquals($info2->getCourriel(), $array2['courriel']);
        $this->assertEquals($info2->getTelephone(), $array2['telephone']);
        // => goodInfos['reponse'] à chaîne vide = pas initialisé en retour array
        $this->assertArrayNotHasKey('reponse', $array2);
        $this->assertEquals($info2->getLogoID(), $array2['logoID']);
        $goodArray = $this->goodInfos;
        unset($goodArray['reponse']);
        $this->assertEquals($array2, $goodArray);
        $this->assertNotEquals($array2, $this->goodInfos);

        /** validation value per field */
        $info2->setDesignation('un changement');
        $this->assertEquals($info2->getDesignation(), 'un changement');

        $this->assertFalse($info2->setSiren('0123456789012'));
        $this->assertFalse($info2->setSiren('012345'));
        $this->assertFalse($info2->setSiren('0a1Z23R45'));
        $this->assertEquals($info2, $info2->setSiren('987654321'));
        $this->assertNotEquals('012345678', $info2->getSiren());
        $this->assertEquals('987654321', $info2->getSiren());

        $this->assertFalse($info2->setSiret('01233456'));
        $this->assertFalse($info2->setSiret("01234567890123456789"));
        $this->assertFalse($info2->setSiret('0a1Z23R45YU12p'));
        $this->assertEquals($info2, $info2->setSiret("98765432154321"));
        $this->assertEquals('98765432154321', $info2->getSiret());

        $this->assertFalse($info2->setTelephone("/01245.23.322"));
        $this->assertFalse($info2->setTelephone("azor.253.456"));
        $this->assertEquals($info2, $info2->setTelephone("+33 4 01 02 03 04"));
        $this->assertEquals($info2, $info2->setTelephone("+33 401020304"));
        $this->assertEquals($info2, $info2->setTelephone("+33 4.01.02.03.04"));
        $this->assertEquals($info2, $info2->setTelephone("04 01 02 03 04"));
        $this->assertEquals($info2, $info2->setTelephone("04.01.02.03.04"));
        $this->assertEquals($info2, $info2->setTelephone("0501020304"));
        $this->assertNotEquals("0401020304", $info2->getTelephone());
        $this->assertEquals("0501020304", $info2->getTelephone());
    }
}