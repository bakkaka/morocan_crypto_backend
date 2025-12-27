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
        // 1. CRÉATION DES DEVISES AVEC TYPE
        $currencies = [];
        
        $currencyData = [
            ['USDT', 'Tether USD', 6, 'crypto'],
            ['MAD', 'Moroccan Dirham', 2, 'fiat'],
            ['BTC', 'Bitcoin', 8, 'crypto'],
            ['ETH', 'Ethereum', 6, 'crypto'],
        ];

        foreach ($currencyData as $data) {
            $currency = new Currency();
            $currency->setCode($data[0]);
            $currency->setName($data[1]);
            $currency->setDecimals($data[2]);
            $currency->setType($data[3]); // ← TYPE AJOUTÉ
            $manager->persist($currency);
            $currencies[$data[0]] = $currency;
            $this->addReference('currency_' . $data[0], $currency);
        }

        // 2. CRÉATION DES MÉTHODES DE PAIEMENT
        $paymentMethods = [];
        
        $methodsData = [
            ['Virement Bancaire CIH', 'Virement vers compte CIH Bank - RIB fourni après accord'],
            ['Virement Bancaire Attijari', 'Virement vers compte Attijariwafa Bank'],
            ['Cash', 'Paiement en espèces - Rencontre dans lieu public sécurisé'],
            ['PayPal', 'Transfert PayPal - Frais à la charge de l\'acheteur'],
            ['Wise', 'Transfert Wise - Anciennement TransferWise'],
            ['Carte Bancaire', 'Paiement par carte via terminal sécurisé'],
        ];

        foreach ($methodsData as $index => $methodData) {
            $method = new PaymentMethod();
            $method->setName($methodData[0]);
            $method->setDetails($methodData[1]);
            $manager->persist($method);
            $paymentMethods[] = $method;
            $this->addReference('payment_method_' . $index, $method);
        }

        // 3. CRÉATION DES UTILISATEURS TEST
        $users = [];
        
        $usersData = [
            [
                'email' => 'trader@moroccancrypto.com',
                'fullName' => 'Ahmed Trader',
                'phone' => '212612345678',
                'password' => 'password',
                'reputation' => 4.8
            ],
            [
                'email' => 'investor@moroccancrypto.com', 
                'fullName' => 'Fatima Investor',
                'phone' => '212698765432',
                'password' => 'password',
                'reputation' => 4.9
            ],
            [
                'email' => 'seller@moroccancrypto.com',
                'fullName' => 'Mehdi Seller',
                'phone' => '212600000000',
                'password' => 'password', 
                'reputation' => 4.5
            ]
        ];

        foreach ($usersData as $index => $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setFullName($userData['fullName']);
            $user->setPhone($userData['phone']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            $user->setReputation($userData['reputation']);
            $user->setIsVerified(true);
            $manager->persist($user);
            $users[] = $user;
            $this->addReference('user_' . $index, $user);
        }

        // 4. CRÉATION DES ANNONCES RÉALISTES
        $adsData = [
            // ACHATS USDT
            [
                'buy', 1500, 10.45, 
                'Virement CIH - Disponible immédiatement', 
                $currencies['USDT'],
                $users[0],
                [$paymentMethods[0], $paymentMethods[2], $paymentMethods[4]],
                'active'
            ],
            [
                'buy', 2500, 10.40,
                'Virement Attijari - RIB fourni après accord',
                $currencies['USDT'], 
                $users[1],
                [$paymentMethods[1], $paymentMethods[3]],
                'active'
            ],
            [
                'buy', 800, 10.48,
                'Cash Casablanca - Centre ville uniquement',
                $currencies['USDT'],
                $users[2],
                [$paymentMethods[2]],
                'active'
            ],
            
            // VENTES USDT  
            [
                'sell', 1200, 10.55,
                'Virement instantané - Toute banque marocaine',
                $currencies['USDT'],
                $users[0], 
                [$paymentMethods[0], $paymentMethods[1], $paymentMethods[5]],
                'active'
            ],
            [
                'sell', 1800, 10.60,
                'PayPal accepté - Frais inclus',
                $currencies['USDT'],
                $users[1],
                [$paymentMethods[3], $paymentMethods[4]],
                'active'
            ],
            [
                'sell', 500, 10.52, 
                'Cash Rabat - Places sécurisées uniquement',
                $currencies['USDT'],
                $users[2],
                [$paymentMethods[2]],
                'paused'
            ],

            // ANNONCES BITCOIN (pour démonstration)
            [
                'buy', 0.1, 350000,
                'Virement bancaire - Montant minimum 0.01 BTC',
                $currencies['BTC'],
                $users[0],
                [$paymentMethods[0], $paymentMethods[1]],
                'active'
            ],
            [
                'sell', 0.05, 355000,
                'Transaction sécurisée - Wallet personnel',
                $currencies['BTC'], 
                $users[1],
                [$paymentMethods[0], $paymentMethods[3], $paymentMethods[4]],
                'active'
            ],
        ];

        foreach ($adsData as $index => $adData) {
            $ad = new Ad();
            $ad->setType($adData[0]);
            $ad->setAmount($adData[1]);
            $ad->setPrice($adData[2]);
            $ad->setPaymentMethod($adData[3]);
            $ad->setCurrency($adData[4]);
            $ad->setUser($adData[5]);
            $ad->setStatus($adData[7]);

            // Ajouter les méthodes de paiement acceptées
            foreach ($adData[6] as $method) {
                $ad->addAcceptedPaymentMethod($method);
            }

            $manager->persist($ad);
            $this->addReference('ad_' . $index, $ad);
        }

        $manager->flush();

        echo "✅ Fixtures créées avec succès:\n";
        echo "   - " . count($currencies) . " devises\n";
        echo "   - " . count($paymentMethods) . " méthodes de paiement\n"; 
        echo "   - " . count($users) . " utilisateurs\n";
        echo "   - " . count($adsData) . " annonces\n";
    }
}