<?php
/**
 * @file
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Affiliate;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAffiliateData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)
    {
        $affiliate = new Affiliate();
        $affiliate->setUrl('http://sensio-labs.com/');
        $affiliate->setEmail('dummy@example.com');
        $affiliate->setIsActive(true);
        $affiliate->setToken('sensio_labs');
        $affiliate->addCategory($this->getReference('category-programming'));
        $em->persist($affiliate);

        $affiliate = new Affiliate();
        $affiliate->setUrl('/');
        $affiliate->setEmail('dummy@example.com');
        $affiliate->setIsActive(false);
        $affiliate->setToken('symfony');
        $affiliate->addCategory($this->getReference('category-design'));
        $affiliate->addCategory($this->getReference('category-programming'));
        $em->persist($affiliate);

        $em->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
