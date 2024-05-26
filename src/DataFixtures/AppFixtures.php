<?php

namespace App\DataFixtures;

use App\Entity\Availability;
use App\Entity\Category;
use App\Entity\Competence;
use App\Entity\Job;
use App\Entity\User;
use App\Entity\YearExperience;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{


    public function load(ObjectManager $manager): void
    {
        // * utilisation de FakerPHP
        $faker = Factory::create('fr_FR');

        $allCompetences = [
            "HTML",
            "CSS",
            "Javascript",
            "Php",
            "Python",
            "Node.js",
            "Java",
            "React",
            "SYmfony",
            "Angular",
            "Ruby",
            "C",
            "C#",
            "Vue.js",
            "Laravel",
            "Joomla",
            "MySql",
            "MongoDB",
            "MariaDB",
            "PostgreSQL",
            "Docker",
            "Apacha",
            "Linux",
            "Nginx",
            "git",
            "Photoshop",
            "Visual Studio Code",
            "Vim",
            "Atom",
            "Emacs",
            "PhpStrom",
            "sublime Text"
        ];

        $allCategory = [
            "language",
            "framework",
            "cms",
            "sgbd",
            "tools",
            "other"
        ];

        foreach ($allCompetences as $competenceName) {
            $newCompetence = new Competence();
            $newCompetence->setTechno($competenceName);

            $manager->persist($newCompetence);
        }


        foreach ($allCategory as $categoryName) {
            $newCategory = new Category();
            $newCategory->setName($categoryName);

            $manager->persist($newCategory);
        }

        for ($i = 1; $i <= 100; $i++) {
            $newUser = new User();

            $newUser->setEmail($faker->email());
            $newUser->setPassword($faker->password(2, 12));
            $newUser->setFirstname($faker->firstName());
            $newUser->setLastname($faker->lastName());
            $newUser->setTown($faker->departmentName());
            $newUser->setCv($faker->file('docs', 'site', false));
            $newUser->setGithub($faker->url());
            $newUser->setLinkedin($faker->url());
            $newUser->setPortfolio($faker->url());
            $newUser->setProfilePicture($faker->imageUrl(640, 480, 'animals', true));
            $newUser->setDescription($faker->paragraphs());

            $newJob = new Job();
            $newJob->setName("Développeur Front-end");
            $newUser->setJob($newJob);
            $manager->persist($newJob);


            $newUser->setPricing($faker->numberBetween(100, 1000));
            $newUser->setCreatedAt(new DateTime());



            $newYearExp = new YearExperience();
            $newYearExp->setYearExp("Intermédiaire (2-7 ans)");
            $newUser->setYearExp($newYearExp);
            $manager->persist($newYearExp);


            $newAvailability = new Availability();
            $newAvailability->setAvailability("3 jours par semaine");
            $newUser->setAvailability($newAvailability);
            $manager->persist($newAvailability);


            $manager->persist($newUser);
        }

        $manager->flush();
    }
}
