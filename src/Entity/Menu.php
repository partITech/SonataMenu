<?php

namespace Partitech\SonataMenu\Entity;

use Doctrine\ORM\Mapping as ORM;
use Partitech\SonataMenu\Model\Menu as BaseMenu;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Table(name: 'sonata_menu')]
#[ORM\Entity(repositoryClass: "Partitech\SonataMenu\Repository\MenuRepository")]
class Menu extends BaseMenu
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
