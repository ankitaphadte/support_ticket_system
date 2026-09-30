<?php

namespace App\Form;

use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TicketType extends AbstractType
{
    public function buildForm(
    FormBuilderInterface $builder,
    array $options
    ): void {
        $builder
            ->add('title', null, [
                'label' => 'Ticket Title',
                'attr' => [
                    'placeholder' => 'Enter a short summary of your issue',
                ],
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Describe your issue in detail...',
                    'rows' => 6,
                ],
            ])

            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'placeholder' => 'Select a category',
                'choices' => [
                    'Technical Issue' => 'TECHNICAL',
                    'Account Issue' => 'ACCOUNT',
                    'Billing Issue' => 'BILLING',
                    'General Enquiry' => 'GENERAL',
                    'Other' => 'OTHER',
                ],
            ])

            ->add('priority', ChoiceType::class, [
                'label' => 'Priority',
                'placeholder' => 'Select priority',
                'choices' => [
                    'Low' => 'LOW',
                    'Medium' => 'MEDIUM',
                    'High' => 'HIGH',
                    'Urgent' => 'URGENT',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
