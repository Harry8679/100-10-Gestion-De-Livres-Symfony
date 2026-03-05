<?php

namespace App\Form;

use App\Entity\Auteur;
use App\Entity\Livre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputClass = 'w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gray-500';

        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du livre',
                'attr'  => ['placeholder' => 'Ex : Le Petit Prince', 'class' => $inputClass],
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
                'attr'     => ['placeholder' => 'Résumé du livre...', 'rows' => 4, 'class' => $inputClass],
            ])
            ->add('anneePublication', IntegerType::class, [
                'label'    => 'Année de publication',
                'required' => false,
                'attr'     => ['placeholder' => '1943', 'class' => $inputClass],
            ])

            // EntityType : génère un <select multiple> relié à la table Auteur
            // C'est le type utilisé pour les relations ManyToMany avec Doctrine
            ->add('auteurs', EntityType::class, [
                'label'        => 'Auteur(s)',
                'class'        => Auteur::class,       // l'entité liée
                'choice_label' => 'nom',               // quelle propriété afficher dans le <select>
                'multiple'     => true,                // plusieurs auteurs possibles
                'expanded'     => true,                // true = checkboxes | false = <select>
                'required'     => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Livre::class]);
    }
}