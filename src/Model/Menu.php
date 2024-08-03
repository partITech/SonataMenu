<?php

namespace Partitech\SonataMenu\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Table(name: 'sonata_menu')]
#[ORM\MappedSuperclass]
#[ORM\InheritanceType('SINGLE_TABLE')]
abstract class Menu implements MenuInterface
{
    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    protected $name;

//    #[ORM\Column(name: 'alias', type: 'string', length: 255)]
//    protected $alias;

    #[ORM\OneToMany(targetEntity: "\Partitech\SonataMenu\Model\MenuItemInterface", mappedBy: 'menu', cascade: ['persist'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    #[Serializer\Groups(['default'])]
    #[Serializer\MaxDepth(1)]
    protected $menuItems;

    public function __construct()
    {
        $this->menuItems = new ArrayCollection();
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

//    public function setAlias(string $alias): self
//    {
//        $this->alias = $alias;
//
//        return $this;
//    }
//
//    public function getAlias(): string
//    {
//        return $this->alias;
//    }

    public function addMenuItem(MenuItemInterface $menuItem): self
    {
        $this->menuItems[] = $menuItem;
        $menuItem->setMenu($this);

        return $this;
    }

    public function removeMenuItem(MenuItemInterface $menuItem): void
    {
        $this->menuItems->removeElement($menuItem);
    }

    public function setMenuItems(Collection $menuItems): self
    {
        $this->menuItems = $menuItems;

        return $this;
    }

    public function getMenuItems(): Collection
    {
        return $this->menuItems;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function __clone()
    {
        if ($this->getId()) {
            $this->setId(null);
            $newMenuItems = new ArrayCollection();
            foreach ($this->menuItems as $menuItem) {
                $clonedMenuItem = clone $menuItem;
                $clonedMenuItem->setMenu($this);
                $newMenuItems->add($clonedMenuItem);
            }
            $this->menuItems = $newMenuItems;
        }
    }
}
