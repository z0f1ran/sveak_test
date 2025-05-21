<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ClientRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Имя',
                'constraints' => [
                    new NotBlank(['message' => 'Пожалуйста, укажите ваше имя.']),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Фамилия',
                'constraints' => [
                    new NotBlank(['message' => 'Пожалуйста, укажите вашу фамилию.']),
                ],
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Номер телефона',
                'constraints' => [
                    new NotBlank(['message' => 'Пожалуйста, укажите ваш номер телефона.']),
                    new Regex([
                        'pattern' => '/^(\+7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/',
                        'message' => 'Некорректный формат российского номера телефона.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Э-почта',
                'constraints' => [
                    new NotBlank(['message' => 'Пожалуйста, укажите вашу э-почту.']),
                    new Email(['message' => 'Адрес э-почты "{{ value }}" некорректен.']),
                ],
            ])
            ->add('education', ChoiceType::class, [
                'label' => 'Образование',
                'choices' => [
                    'Среднее образование' => 'Среднее образование',
                    'Специальное образование' => 'Специальное образование',
                    'Высшее образование' => 'Высшее образование',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Пожалуйста, выберите ваше образование.']),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Я даю согласие на обработку моих личных данных',
                'mapped' => false, // Это поле не будет сохраняться в сущность Client напрямую
                'constraints' => [
                    new IsTrue(['message' => 'Вы должны согласиться на обработку личных данных.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Мы не связываем эту форму напрямую с сущностью Client,
            // так как поле agreeTerms не является частью Client.
            // Мы будем обрабатывать данные формы вручную в контроллере.
            'data_class' => null,
        ]);
    }
} 