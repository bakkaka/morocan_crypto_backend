<?php

namespace App\DataFixtures;

use App\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CurrencyFixtures extends Fixture
{
   public function load(ObjectManager $manager): void
{
    $currencies = [
        // Cryptos
        ['USDT', 'Tether USD', 'crypto', 2],
        ['BTC', 'Bitcoin', 'crypto', 8],
        ['ETH', 'Ethereum', 'crypto', 18],
        ['BNB', 'Binance Coin', 'crypto', 8],
        ['XRP', 'Ripple', 'crypto', 6],
        ['SOL', 'Solana', 'crypto', 9],
        ['ADA', 'Cardano', 'crypto', 6],
        ['DOT', 'Polkadot', 'crypto', 10],
        ['MATIC', 'Polygon', 'crypto', 18],
        
        // Fiats
        ['MAD', 'Dirham Marocain', 'fiat', 2],
        ['EUR', 'Euro', 'fiat', 2],
        ['USD', 'Dollar US', 'fiat', 2],
    ];

    foreach ($currencies as [$code, $name, $type, $decimals]) {
        // Vérifier si la devise existe déjà
        $existing = $manager->getRepository(Currency::class)->findOneBy(['code' => $code]);
        
        if (!$existing) {
            $currency = new Currency();
            $currency->setCode($code);
            $currency->setName($name);
            $currency->setType($type);
            $currency->setDecimals($decimals);
            $currency->setCreatedAt(new \DateTimeImmutable());
            
            $manager->persist($currency);
            $this->addReference('currency_' . strtolower($code), $currency);
        }
    }

    $manager->flush();
}
}