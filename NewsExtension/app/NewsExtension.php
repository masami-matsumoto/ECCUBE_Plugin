<?php

namespace Customize\Entity;

use Eccube\Entity\News;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="plg_news_extension")
 * @ORM\Entity
 */
class NewsExtension
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Eccube\Entity\News")
     * @ORM\JoinColumn(name="news_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $news;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image_pc;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image_sp;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $create_date;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $update_date;

    // --- Getter/Setter ---
    public function getNews()
    {
        return $this->news;
    }

    public function setNews($news)
    {
        $this->news = $news;
        return $this;
    }

    public function getImagePc()
    {
        return $this->image_pc;
    }

    public function setImagePc($imagePc)
    {
        $this->image_pc = $imagePc;
        return $this;
    }

    public function getImageSp()
    {
        return $this->image_sp;
    }

    public function setImageSp($imageSp)
    {
        $this->image_sp = $imageSp;
        return $this;
    }

    public function getCreateDate()
    {
        return $this->create_date;
    }

    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;
        return $this;
    }

    public function getUpdateDate()
    {
        return $this->update_date;
    }

    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;
        return $this;
    }
}