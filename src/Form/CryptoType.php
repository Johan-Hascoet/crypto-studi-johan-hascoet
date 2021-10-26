<?php

namespace App\Form;

use App\Entity\Cryptocurrency;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CryptoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', ChoiceType::class, array(
                'placeholder' => 'Sélectionner une crypto',
                'required' => true,
                'choices' => [
                    'Bitcoin' => 'BTC',
                    'Ethereum' => 'ETH',
                    'Cardano' => 'ADA',
                    'XRP' => 'XRP',
                    'Polkamarkets' => 'POLK',
                    'Boson Protocol' => 'BOSON',
                    'MultiVAC' => 'MTV',
                ]
            ))
            ->add('quantity', NumberType::class, array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Quantité',
                )
            ))
            ->add('price', NumberType::class, array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Prix d\'achat',
                )
            ))
            ->add('Ajouter', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cryptocurrency::class,
        ]);
    }


}
