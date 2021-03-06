<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\ORM\Annotation as ORM;

class DDC381Test extends \Doctrine\Tests\OrmFunctionalTestCase
{
    protected function setUp()
    {
        parent::setUp();

        try {
            $this->schemaTool->createSchema(
                [
                $this->em->getClassMetadata(DDC381Entity::class),
                ]
            );
        } catch (\Exception $e) {
        }
    }

    public function testCallUnserializedProxyMethods()
    {
        $entity = new DDC381Entity();

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();
        $persistedId = $entity->getId();

        $entity = $this->em->getReference(DDC381Entity::class, $persistedId);

        // explicitly load proxy (getId() does not trigger reload of proxy)
        $id = $entity->getOtherMethod();

        $data = serialize($entity);
        $entity = unserialize($data);

        self::assertEquals($persistedId, $entity->getId());
    }
}

/**
 * @ORM\Entity
 */
class DDC381Entity
{
    /**
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }

    public function getOtherMethod()
    {
    }
}
