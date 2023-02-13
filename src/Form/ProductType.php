<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Platform;
use App\Entity\Conceptor;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class)
            ->add('description', TextType::class)
            ->add('releaseDate',  DateType::class, [
                'years' => range(1980,2099),
                'label' => 'Date de sortie',
                'format' => 'ddMMyyyy'
            ])
            ->add('stock', IntegerType::class)
            ->add('price', MoneyType::class, [
                'label' => 'Prix',

            ])
            ->add('new',  ChoiceType::class, [
                'choices' => [
                    'Neuf' => true,
                    'Occasion' => false,
                ],
                'label' => 'État du produit',
            ])

            ->add('Conceptor', EntityType::class, [
                'class' => Conceptor::class,
                'label' => 'Concepteur'
            ])

            ->add('Category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Categorie'
            ])


            ->add('Genres', EntityType::class, [
                'class' => Genre::class,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'choice_label' => function ($label) {
                    return $label->getLabel();
                },
                'by_reference' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('g')
                        ->orderBy('g.label', 'ASC');
                }


            ])


            ->add('Platforms', EntityType::class, [
                'class' => Platform::class,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'choice_label' => function ($label) {
                    return $label->getLabel();
                },
                'by_reference' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.label', 'ASC');
                }

                
                
            ])
            ->add('pictures' , FileType::class , [
                'multiple' => true,
                'mapped' => false,
                'label' => 'Ajouter des images',
                'required' => false
                
                
            ])




            
                
            ->add('Valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
