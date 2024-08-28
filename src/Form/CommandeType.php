<?php
namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomClient', TextType::class, [
            'label' => 'Nom du Client',
        ]);

        foreach ($options['materials'] as $material) {
            $commandeMaterial = $options['data']->getCommandeMaterials()->filter(function ($item) use ($material) {
                return $item->getMaterial() === $material;
            })->first();
    
            $builder->add('material_' . $material->getId(), IntegerType::class, [
                'label' => $material->getNom() . ' (Disponible: ' . $material->getQuantite() . ')',
                'mapped' => false,
                'attr' => ['min' => 0, 'max' => $material->getQuantite()],
                'data' => $commandeMaterial ? $commandeMaterial->getQuantite() : 0,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'materials' => [],
        ]);
    }
}