<?php

namespace Customize\Form\Type\Admin;

use Eccube\Form\Type\Admin\NewsType as BaseNewsType;
use Eccube\Common\EccubeConfig;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Eccube\Entity\News;
use Customize\Entity\InformationCategory;
use Doctrine\ORM\EntityRepository;

class NewsType extends BaseNewsType
{
    public function __construct(EccubeConfig $eccubeConfig)
    {
        parent::__construct($eccubeConfig);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // 親フォームの基本フィールドを継承
        parent::buildForm($builder, $options);
        
        // カテゴリ選択（複数可、最大10件まで）
        $builder->add('informationCategories', EntityType::class, [
            'class' => InformationCategory::class,
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true, // チェックボックス形式
            'label' => '情報カテゴリ',
            'required' => false,
            'mapped' => false, // 手動処理
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->orderBy('c.display_order', 'ASC')
                    ->setMaxResults(10);
            },
            'eccube_form_options' => [
                'auto_render' => true,
            ],
        ]);

        // 既存ファイル名表示用（読み取り専用）
        $builder->add('current_image_pc', TextType::class, [
            'label' => '現在のPC画像',
            'required' => false,
            'mapped' => false,
            'attr' => ['readonly' => true, 'placeholder' => 'ファイルが登録されていません'],
            'eccube_form_options' => [
                'auto_render' => true,
            ],
        ]);
        
        // 画像アップロード
        $builder->add('image_pc', FileType::class, [
            'label' => '画像PC（大画像）- 新しいファイル',
            'required' => false,
            'mapped' => false, // アップロード処理はControllerで
            'eccube_form_options' => [
                'auto_render' => true,
            ],
        ]);
        
        // 既存ファイル名表示用（読み取り専用）
        $builder->add('current_image_sp', TextType::class, [
            'label' => '現在のSP画像',
            'required' => false,
            'mapped' => false,
            'attr' => ['readonly' => true, 'placeholder' => 'ファイルが登録されていません'],
            'eccube_form_options' => [
                'auto_render' => true,
            ],
        ]);
        
        $builder->add('image_sp', FileType::class, [
            'label' => '画像SP（スマホ画像）- 新しいファイル',
            'required' => false,
            'mapped' => false,
            'eccube_form_options' => [
                'auto_render' => true,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}