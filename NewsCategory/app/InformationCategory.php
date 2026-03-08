<?php

namespace Customize\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="plg_information_category")
 * @ORM\Entity(repositoryClass="Customize\\Repository\\InformationCategoryRepository")
 */
class InformationCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $display_order = 0;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $create_date;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $update_date;

    // --- Getter/Setter ---
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    public function getDisplayOrder()
    {
        return $this->display_order;
    }
    public function setDisplayOrder($displayOrder)
    {
        $this->display_order = $displayOrder;
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

