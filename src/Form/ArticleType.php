<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\File;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Categorie;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'label' => 'Image (JPG, PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG ou PNG)',
                    ])
                ],
            ])
            ->add('nom')
            ->add('disponibilite')
            ->add('prix', NumberType::class)
            ->add('quantite', null, [
                'mapped' => false,
                'label' => 'Quantité en stock',
                'required' => false,
                'data' => $options['quantite'] ?? 0,
            ])
            ->add('categorie', EntityType::class, [  // 🔹 Ajout du champ catégorie
                'class' => Categorie::class,
                'choice_label' => 'libelle', // Affichage du nom de la catégorie
                'placeholder' => 'Choisissez une catégorie',
                'required' => false, // Optionnel
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'quantite' => 0,
        ]);
    }
}
