<?php

namespace App\Form;

use App\Entity\PurchaseOrder;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PurchaseOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pc', TextType::class, [
                'label' => 'Code Postal',
                'attr' => array(
                    'placeholder' => ' 00000'
                )
            ])

            ->add('city',ChoiceType::class, [
                'label' => 'Ville',
                'choices' => [
                    'Votre ville' =>'Votre ville'
                ]
                // 'expanded' => false,
                // 'multiple' => false,

            ])

            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                [$this, 'onPreSubmit']
            )

            ->add('adress', TextType::class, [
                'label' => 'Adresse complète',
                'attr' => array(
                    'placeholder' => ' n° de la rue et adresse'
                )
            ])

            //     ->add('submit' , SubmitType::class , [
            //         'label' => 'Valider la commande'
            //     ])
        ;
    }

    public function onPreSubmit(FormEvent $event)
   {
       $input = $event->getData()['city'];
       $event->getForm()->add('city', ChoiceType::class,
           ['choices' => [$input]]);
   }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PurchaseOrder::class,
        ]);
    }
}