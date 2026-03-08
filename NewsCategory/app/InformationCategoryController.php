<?php

namespace Customize\Controller\Admin;

use Eccube\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Customize\Entity\InformationCategory;
use Customize\Form\Type\Admin\InformationCategoryType;

/**
 * @Route("/%eccube_admin_route%/content/information_category")
 */
class InformationCategoryController extends AbstractController
{
    /**
     * @Route("/", name="admin_content_information_category")
     */
    public function index(EntityManagerInterface $em)
    {
        $categories = $em->getRepository(InformationCategory::class)->findBy([], ['display_order' => 'ASC']);
        return $this->render('information_category/index.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/new", name="admin_content_information_category_new")
     */
    public function new(Request $request, EntityManagerInterface $em)
    {
        $category = new InformationCategory();
        $form = $this->createForm(InformationCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCreateDate(new \DateTime());
            $category->setUpdateDate(new \DateTime());
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'カテゴリを追加しました。');
            return $this->redirectToRoute('admin_content_information_category');
        }

        return $this->render('information_category/edit.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_content_information_category_edit")
     */
    public function edit($id, Request $request, EntityManagerInterface $em)
    {
        $category = $em->getRepository(InformationCategory::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException();
        }
        $form = $this->createForm(InformationCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setUpdateDate(new \DateTime());
            $em->flush();
            $this->addFlash('success', 'カテゴリを更新しました。');
            return $this->redirectToRoute('admin_content_information_category');
        }

        return $this->render('information_category/edit.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_content_information_category_delete", methods={"POST"})
     */
    public function delete($id, Request $request, EntityManagerInterface $em)
    {
        $category = $em->getRepository(InformationCategory::class)->find($id);
        if ($category) {
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'カテゴリを削除しました。');
        }
        return $this->redirectToRoute('admin_content_information_category');
    }

    /**
     * @Route("/sort", name="admin_content_information_category_sort", methods={"POST"})
     */
    public function sort(Request $request, EntityManagerInterface $em)
    {
        $repo = $em->getRepository(InformationCategory::class);
        $data = $request->request->all();
        foreach ($data as $id => $order) {
            $category = $repo->find($id);
            if ($category) {
                $category->setDisplayOrder($order);
            }
        }
        $em->flush();
        return new Response('OK');
    }
}