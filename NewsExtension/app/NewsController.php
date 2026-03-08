<?php

namespace Customize\Controller\Admin\Content;

use Eccube\Controller\Admin\Content\NewsController as BaseNewsController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Customize\Entity\News;
use Customize\Entity\NewsExtension;
use Customize\Form\Type\Admin\NewsType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class NewsController extends BaseNewsController
{
    public function new(Request $request, EntityManagerInterface $em)
    {
        throw new \Exception('Customize NewsController is called!');       
        // $News = new News();
        // $form = $this->createForm(NewsType::class, $News);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     // 画像アップロード処理
        //     $hasImages = $this->handleImageUpload($form, $News, 'image_pc') 
        //               || $this->handleImageUpload($form, $News, 'image_sp');
            
        //     // 日時設定
        //     $now = new \DateTime();
        //     $News->setCreateDate($now);
        //     $News->setUpdateDate($now);
            
        //     // NewsExtensionが作成されている場合は永続化
        //     if ($News->getNewsExtension()) {
        //         $News->getNewsExtension()->setUpdateDate($now);
        //         $em->persist($News->getNewsExtension());
        //     }
            
        //     $em->persist($News);
        //     $em->flush();
            
        //     $this->addFlash('success', '新着情報を追加しました。');
        //     return $this->redirectToRoute('admin_content_news');
        }

        return $this->render('@admin/Content/news/new.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * 画像ファイルのアップロード処理
     * @return bool アップロードが実行されたかどうか
     */
    private function handleImageUpload($form, News $News, string $fieldName): bool
    {
        /** @var UploadedFile $imageFile */
        $imageFile = $form[$fieldName]->getData();
        
        if (!$imageFile) {
            return false;
        }

        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename . '_' . uniqid() . '.' . $imageFile->guessExtension();
        
        try {
            $uploadDir = $this->getParameter('eccube_save_image_dir');
            $imageFile->move($uploadDir, $newFilename);
            
            // エンティティにファイル名を設定（NewsExtensionが自動作成される）
            if ($fieldName === 'image_pc') {
                $News->setImagePc($newFilename);
            } elseif ($fieldName === 'image_sp') {
                $News->setImageSp($newFilename);
            }
            
            return true;
        } catch (\Exception $e) {
            $this->addFlash('error', 'ファイルのアップロードに失敗しました: ' . $e->getMessage());
            return false;
        }
    }
}