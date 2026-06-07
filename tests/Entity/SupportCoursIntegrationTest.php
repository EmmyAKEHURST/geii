<?php

namespace App\Tests\Entity;

use App\Entity\SupportCours;
use App\Tests\IntegrationTestCase;
use DateTime;

class SupportCoursIntegrationTest extends IntegrationTestCase
{
    /**
     * Vérifie qu'un support de cours est persisté avec son titre, son chemin de fichier et sa date de dépôt.
     */
    public function testPersistanceSupportCours(): void
    {
        $support = (new SupportCours())
            ->setTitre('Introduction aux microcontrôleurs')
            ->setFichierPath('uploads/cours/microcontroleurs_intro.pdf')
            ->setDateDepot(new DateTime('2024-09-01'))
        ;

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
