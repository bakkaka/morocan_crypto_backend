<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Currency;
use App\Entity\PaymentMethod;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer des devises
        $usdt = new Currency();
        $usdt->setCode('USDT');
        $usdt->setName('Tether USD');
        $manager->persist($usdt);

        $mad = new Currency();
        $mad->setCode('MAD');
        $mad->setName('Moroccan Dirham');
        $manager->persist($mad);

        // Créer des méthodes de paiement
        $paymentMethods = [];
        $methodsData = [
            ['Virement Bancaire', 'CIH, Attijari, BMCE'],
            ['Cash', 'Rencontre en personne'],
            ['PayPal', 'Transfert PayPal'],
            ['Wise', 'Transfert Wise'],
            ['Carte Bancaire', 'Paiement par carte']
        ];

        foreach ($methodsData as $methodData) {
            $method = new PaymentMethod();
            $method->setName($methodData[0]);
            $method->setDetails($methodData[1]);
            $manager->persist($method);
            $paymentMethods[] = $method;
        }

        // Créer un utilisateur test
        $user = new User();
        $user->setEmail('test@moroccancrypto.com');
        $user->setFullName('Test User');
        $user->setPhone('212612345678');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

        // Créer des annonces de test
        $adsData = [
            ['buy', 1000, 10.5, 'Virement CIH', $usdt, [$paymentMethods[0], $paymentMethods[2]]],
            ['sell', 500, 10.3, 'Cash Casablanca', $usdt, [$paymentMethods[1]]],
            ['buy', 2000, 10.4, 'Virement Attijari', $usdt, [$paymentMethods[0], $paymentMethods[4]]],
            ['sell', 750, 10.6, 'PayPal', $usdt, [$paymentMethods[2]]],
        ];

        foreach ($adsData as $adData) {
            $ad = new Ad();
            $ad->setType($adData[0]);
            $ad->setAmount($adData[1]);
            $ad->setPrice($adData[2]);
            $ad->setPaymentMethod($adData[3]);
            $ad->setCurrency($adData[4]);
            $ad->setUser($user);
            $ad->setStatus('active');

            // Ajouter les méthodes de paiement acceptées
            foreach ($adData[5] as $method) {
                $ad->addAcceptedPaymentMethod($method);
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}