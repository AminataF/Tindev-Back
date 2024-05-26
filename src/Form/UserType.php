<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                "attr" => [
                    'placeholder' => "johnDoe@email.com",
                    'label' => 'E-mail'
                ]
            ]
            )
            ->add('roles', ChoiceType::class, [
                "choices" => [
                    "ADMIN" => "ROLE_ADMIN",
                    "USER" => "ROLE_USER"
                ], 
                'multiple' => true,
                'expanded' => true
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                "attr" => [
                    "placeholder" => "votre mot de passe"
                ],
                "mapped" => false
            ])
            ->add('firstname', TypeTextType::class, [
                
            ])
            ->add('lastname', TypeTextType::class)
            ->add('town', TypeTextType::class)
            ->add('cv', TypeTextType::class)
            ->add('github', TypeTextType::class)
            ->add('linkedin', TypeTextType::class)
            ->add('portfolio', TypeTextType::class)
            ->add('profilePicture', TypeTextType::class)
            ->add('description', TextareaType::class)
            ->add('pricing', IntegerType::class)
            // ->add('competences', CollectionType::class, [
            //         "entry_type" => CompetenceType::class,
            //     // "choice_label" => "compétences",
            //     // 'multiple' => false,
            //     // 'expanded' => false
            // ])
            // ->add('job', EntityType::class, [
            //     "class" => Job::class,
            //     // "choice_label" => "Poste occupée",
              
            // ])
            // ->add('yearExp', EntityType::class, [
            //     "class" => YearExperience::class,
            //     // "choice_label" => "Années d'expériences",
               
            // ])
            // ->add('availability', EntityType::class, [
            //     "class" => Availability::class,
            //     // "choice_label" => "Disponibilités",
                
            // ])
            // ->add('updateAt', DateType::class, [
            //     // "choices" => date('NOW')
            // ])
            // // ->add('updateAt')
            // // ->add('like1')
            // // ->add('like2')
        ;
            
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
