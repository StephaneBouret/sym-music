<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Ticket;
use DateTimeImmutable;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use libphonenumber\PhoneNumberUtil;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $phoneNumberUtil = PhoneNumberUtil::getInstance(); // Instance de PhoneNumberUtil
        $admin = new User();

        $hash = $this->passwordHasher->hashPassword($admin, "password");

        // Générer un numéro de téléphone pour l'admin
        $adminRawPhoneNumber = $faker->mobileNumber();
        $adminPhoneNumberObject = $phoneNumberUtil->parse($adminRawPhoneNumber, 'FR');

        $admin->setFirstname('admin')
            ->setLastname('admin')
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN'])
            ->setAdress($faker->streetAddress)
            ->setPostalCode($faker->postcode)
            ->setCity($faker->city)
            ->setEmail("admin@gmail.com")
            ->setPhone($adminPhoneNumberObject);

        $manager->persist($admin);

        $users = [];
        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $hash = $this->passwordHasher->hashPassword($user, "password");

            // Générer un numéro de téléphone différent pour chaque utilisateur
            $rawPhoneNumber = $faker->mobileNumber();
            $phoneNumberObject = $phoneNumberUtil->parse($rawPhoneNumber, 'FR');

            $user->setEmail("user$u@gmail.com")
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setPassword($hash)
                ->setAdress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setCity($faker->city)
                ->setPhone($phoneNumberObject);

            $manager->persist($user);
            $users[] = $user;
        }

        $tickets = $manager->getRepository(Ticket::class)->findAll();

        for ($p = 0; $p < mt_rand(20, 40); $p++) {
            $purchase = new Purchase();
            $purchase->setTotal(mt_rand(5600, 19900))
                ->setFullName($faker->name)
                ->setEmail($faker->freeEmail)
                ->setUser($faker->randomElement($users))
                ->setPurchasedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months')));

            $selectedTickets = $faker->randomElements($tickets, mt_rand(1, 3));

            foreach ($selectedTickets as $ticket) {
                // $purchase->addTicket($ticket);
                $purchaseItem = new PurchaseItem;
                $purchaseItem->setTicket($ticket)
                    ->setQuantity(mt_rand(1, 2))
                    ->setTicketName($ticket->getFullName())
                    ->setTicketPrice($ticket->getPrice())
                    ->setTotal(
                        $purchaseItem->getTicketPrice() * $purchaseItem->getQuantity()
                    )
                    ->setPurchase($purchase);
                $manager->persist($purchaseItem);
            }

            if ($faker->boolean(90)) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            }


            $manager->persist($purchase);
        }

        $manager->flush();
    }
}
