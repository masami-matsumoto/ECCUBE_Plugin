<?php

namespace Customize\Entity;

use Eccube\Entity\News as BaseNews;
use Customize\Entity\InformationCategory;
use Customize\Entity\NewsExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

class News extends BaseNews
{
    /**
     * @ORM\ManyToMany(targetEntity="Customize\Entity\InformationCategory")
     * @ORM\JoinTable(name="plg_news_information_category",
     *      joinColumns={@ORM\JoinColumn(name="news_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="information_category_id", referencedColumnName="id")}
     * )
     */
    private $informationCategories;

    /**
     * @ORM\OneToOne(targetEntity="Customize\Entity\NewsExtension", mappedBy="news", cascade={"persist", "remove"})
     */
    private $newsExtension;

    public function __construct()
    {
        $this->informationCategories = new ArrayCollection();
    }

    // --- InformationCategory (Many-to-Many) ---
    public function getInformationCategories()
    {
        return $this->informationCategories;
    }
    
    public function addInformationCategory(InformationCategory $category)
    {
        if (!$this->informationCategories->contains($category)) {
            $this->informationCategories[] = $category;
        }
        return $this;
    }
    
    public function removeInformationCategory(InformationCategory $category)
    {
        $this->informationCategories->removeElement($category);
        return $this;
    }

    // --- NewsExtension (One-to-One) ---
    public function getNewsExtension()
    {
        return $this->newsExtension;
    }
    
    public function setNewsExtension($newsExtension)
    {
        $this->newsExtension = $newsExtension;
        if ($newsExtension && $newsExtension->getNews() !== $this) {
            $newsExtension->setNews($this);
        }
        return $this;
    }

    // --- Helper methods for images (透過的アクセス) ---
    public function getImagePc()
    {
        return $this->newsExtension ? $this->newsExtension->getImagePc() : null;
    }
    
    public function setImagePc($imagePc)
    {
        $this->ensureNewsExtension();
        $this->newsExtension->setImagePc($imagePc);
        return $this;
    }

    public function getImageSp()
    {
        return $this->newsExtension ? $this->newsExtension->getImageSp() : null;
    }
    
    public function setImageSp($imageSp)
    {
        $this->ensureNewsExtension();
        $this->newsExtension->setImageSp($imageSp);
        return $this;
    }

    /**
     * NewsExtensionインスタンスの確保
     */
    private function ensureNewsExtension()
    {
        if (!$this->newsExtension) {
            $this->newsExtension = new NewsExtension();
            $this->newsExtension->setNews($this);
            $now = new \DateTime();
            $this->newsExtension->setCreateDate($now);
            $this->newsExtension->setUpdateDate($now);
        }
    }
}