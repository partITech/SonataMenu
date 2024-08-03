<?php

namespace Partitech\SonataMenu\Entity;

use Doctrine\ORM\Mapping as ORM;
use Partitech\SonataMenu\Model\MenuItem as BaseMenuItem;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Table(name: 'sonata_menu_item')]
#[ORM\Entity(repositoryClass: "Partitech\SonataMenu\Repository\MenuItemRepository")]
class MenuItem extends BaseMenuItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
