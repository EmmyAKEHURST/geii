<?php

namespace App\Tests\Integration\Entity;

use App\Entity\SupportCours;
use App\Tests\Integration\IntegrationTestCase;

class SupportCoursIntegrationTest extends IntegrationTestCase
{
    public function testPersistanceSupportCours(): void
    {
        $support = new SupportCours();
        $support->setTitre('Introduction aux microcontrôleurs');
        $support->setFichierPath('uploads/cours/microcontroleurs_intro.pdf');
        $support->setDateDepot(new \DateTime('2024-09-01'));

        $this->em->persist($support);
        $this->em->flush();

        $this->assertNotNull($support->getId());

        $this->em->clear();

        $trouve = $this->em->find(SupportCours::class, $support->getId());
        $this->assertNotNull($trouve);
        $this->assertSame('Introduction aux microcontrôleurs', $trouve->getTitre());
        $this->assertSame('uploads/cours/microcontroleurs_intro.pdf', $trouve->getFichierPath());
        $this->assertSame('2024-09-01', $trouve->getDateDepot()->format('Y-m-d'));
    }
}
