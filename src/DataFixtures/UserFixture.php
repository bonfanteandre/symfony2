<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('usuario');
        $user->setPassword('$argon2i$v=19$m=1024,t=2,p=2$WlMxU1FRTzg4d1BHMVNWWA$9vmXbGAO+y6OyP28ilA2mywLFmOLOgsBnVJuhwemI');

        $manager->persist($user);

        $manager->flush();
    }
}
