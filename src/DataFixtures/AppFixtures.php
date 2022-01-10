<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        
		 // Products
		$allProducts = [];
        for($i=0; $i<30; $i++) 
		{
            $product = new Product();
            $product->setName($faker->words(2, true));
            $product->setPrice($faker->numberBetween(1000, 50000));
			
            $manager->persist($product);
            $allProducts[] = $product;
        }
        $manager->flush();

        // User
		$user = new User();
        $user->setEmail('yo@yo.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->encoder->hashPassword($user, 'yoyoyo'));

        $manager->persist($user);
        $manager->flush();

        // Cart
		$cart = new Cart();
        $cart->setUser($user);
        $cart->setStatus('active');
        $cart->addProduct($faker->randomElement($allProducts));
        $cart->addProduct($faker->randomElement($allProducts));
        $cart->addProduct($faker->randomElement($allProducts));

        $manager->persist($cart);
        $manager->flush();
    }
}
