<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Product;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $test_password = 'test';
        $factory = $this->container->get('security.encoder_factory');
        for ($count = 0; $count < 20; $count++) {
            $user = new User();
            $user->setFirstName('Albert_'.$count);
            $user->setLastName("Houbre_".$count);
            $user->setEmail('admin'.$count.'@admin.com');
            $user->setFirstname('Zozo_'.$count);
            $user->setRoles(array('ROLE_ADMIN', 'ROLE_USER'));
            $user->setAddressLine1("line one of the address");
            $user->setAddressLine2("line two of the address");
            $user->setZipCode("34000");
            $user->setRegion("34");
            $user->setCity("Montpellier");
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($test_password, $user->getSalt());
            $user->setPassword($password);

            $manager->persist($user);
        }
        $manager->flush();



//         $product = new Product();
//         $manager->persist($product);
//
//        $manager->flush();
    }
}
