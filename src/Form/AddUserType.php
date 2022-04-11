<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('telephoneNr', TextType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The pasword fields must match.',
                'required' => true,
                'first_options' => ['label' => 'Password: '],
                'second_options' => ['label' => 'Repeat Password: '],
            ])
            ->add('cnp', TextType::class)
            ->add('role', TextType::class)
            ->add('submit', SubmitType::class);
    }
}
