<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Conceptor;
use App\Entity\Genre;
use App\Entity\Platform;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface as FormFormBuilderInterface;

class SearchType extends AbstractType
{

    public function buildForm(FormFormBuilderInterface $builder, array $options)
    {
        $builder
            //On ajout un champ pour taper notre recherche
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])

            // On ajoute les champs qui permettent de filtrer par Genres , Plateformes etc..
            ->add('genres', EntityType::class, [
                'label'  => false,
                'required' => false,
                'class' => Genre::class,
                'expanded' => true,
                'multiple' => true
            ])

            ->add('platforms', EntityType::class, [
                'label'  => false,
                'required' => false,
                'class' => Platform::class,
                'expanded' => true,
                'multiple' => true
            ])

            ->add('conceptor', EntityType::class, [
                'label'  => false,
                'required' => false,
                'class' => Conceptor::class,
                'expanded' => true,
                'multiple' => true
            ])

            ->add('category', EntityType::class, [
                'label'  => false,
                'required' => false,
                'class' => Category::class,
                'expanded' => true,
                'multiple' => true
            ])

            //Permet de definir le prix maximum et le prix minimum
            ->add('min', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix min'
                ]
            ])

            ->add('max', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix max'
                ]
            ])


            //Permet de filtrer un produit si il est en occasion ou pas ( BOOLEAN )
            ->add('new', CheckboxType::class, [
                'label' => 'Occasion',
                'required' => false,
            ]);
    }


    //Permet de configurer les options liées aux formulaire 
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            // On veut que les paramètres passent l'url dans le cas ou l'utilisateur souhaite partager une recherche 
            'method' => 'GET',
            // Dans le carde du formulaire de recherche pas de risque de faille XSS
            'crsf_protection' => false

        ]);
    }


    // Permet d'avoir un url plus propre , retourne une simple chaine de caractère vide au lieu de retourner un tableau de SearchData
    public function getBlockPrefix()
    {
        return '';
    }
}
