<?php

namespace App\DataFixtures;

use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Product;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $prod = [];
        $author = new User();
        $author->setFirstName('Albert_')
            ->setLastName("Houbre_")
            ->setEmail($faker->email)
            ->setFirstname('Zozo_')
            ->setRoles(array('ROLE_ADMIN', 'ROLE_USER'))
            ->setAddressLine1("line one of the address")
            ->setAddressLine2("line two of the address")
            ->setZipCode("34000")
            ->setRegion("34")
            ->setCity("Montpellier")
            ->setType('producer')
            //            $encoded = $encoder->encodePassword($user, $test_password);
            //            ->setPassword($encoded);
            ->setPassword('test');
        for ($j = 0; $j < 5; $j++) {
            $cat = new Category();
            $cat->setName("Categorie_".$j);
            for ($i = 0; $i < 100; $i++) {
                $prod[$i] = new Product();
                $author->addProduct($prod[$i]);
                $prod[$i]->setName("Product_".$i)
                    ->setImage("https://picsum.photos/id/".$i."/400/600")
                    ->setProductRef("#ref".$i.rand(1, 100000))
                    ->setQuantity(($i +1) * 4)
                    ->setUnit("par 6")
                    ->setCategory($cat)
                    ->setPrice(15.00);

                $manager->persist($prod[$i]);
            }
            $manager->persist($cat);
        }
        $manager->persist($author);

        $test_password = 'test';

//        $user = [];
//        for ($count = 0; $count < 20; $count++) {
//            $user = new User();
//            $user->setFirstName('Albert_'.$count)
//                ->setLastName("Houbre_".$count)
//                ->setEmail('admin'.$count.'@admin.com')
//                ->setFirstname('Zozo_'.$count)
//                ->setRoles(array('ROLE_ADMIN', 'ROLE_USER'))
//                ->setAddressLine1("line one of the address")
//                ->setAddressLine2("line two of the address")
//                ->setZipCode("34000")
//                ->setRegion("34")
//                ->setCity("Montpellier")
//                ->setType('producer')
//    //            $encoded = $encoder->encodePassword($user, $test_password);
//    //            ->setPassword($encoded);
//                ->setPassword($test_password);
//
//            foreach ($prod as $key => $val ){
//                $user-> addProduct($prod[$key]);
//            }
//
//            $manager->persist($user);
//        }

        $manager->flush();







//         $product = new Product();
//         $manager->persist($product);
//
//        $manager->flush();
    }
}
