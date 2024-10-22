<?php

namespace Partitech\SonataMenu\Admin;

use Partitech\SonataMenu\Model\MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MenuItemAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'sonata/menu/menu-item';

    protected $menuClass;

    private $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    private $slugify;

    public function setSlugify($slugify)
    {
        $this->slugify = $slugify;
    }

    public function __construct(string $code, string $class, string $baseControllerName, string $menuClass)
    {
        parent::__construct(
            $code,
            $class,
            $baseControllerName
        );

        $this->menuClass = $menuClass;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('toggle', $this->getRouterIdParameter().'/toggle');
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $subject = $this->getSubject();

        $menu = $subject->getMenu();

        if (!$menu) {
            $request = $this->getRequest();

            $id = $request->get('menu', '');

            if (!empty(intval($id))) {
                $menuManager = $this->container->get('sonata_menu.manager');

                $menu = $menuManager->load($id);
            }
        }

        $formMapper
            ->tab('Defaut');


        $formMapper
            ->with('config.label_menu_item', ['class' => 'col-md-6', 'translation_domain' => 'PartitechSonataMenuBundle'])
                ->add('name', TextType::class,
                    [
                        'label' => 'config.label_name',
                    ],
                    [
                        'translation_domain' => 'PartitechSonataMenuBundle',
                    ]
                );
        if ($this->container->hasParameter('sonata.page.page.class')) {
            $subject = $this->getSubject();
            //$siteId=$menu->getSite();

            $menuItemClass = $this->container->getParameter('sonata_menu.entity.menu_item');

            $em = $this->getModelManager()->getEntityManager($menuItemClass);
            $builder = $em->createQueryBuilder('m');
            $query = $builder->select('m')
                ->from($menuItemClass, 'm')
                ->where('m.menu = '.$menu->getId())
                ->getQuery();

            $menuItems = $query->getResult();


            $choices = [];

            foreach ($menuItems as $item) {
                $choices['config.label_select'] = null;
                $choices[$item->getId().' : '.ucfirst($item->getName())] = $item;
            }

            $formMapper->add('parent', ChoiceType::class,
                [
                    'label' => 'config.label_parent',
                    'required' => false,
                    'choices' => $choices,
                    'data' => $this->getSubject()->getParent(),
                    'empty_data' => null,
                ],
                [
                    'translation_domain' => 'PartitechSonataMenuBundle',
                ]
            );
        }else{
            $formMapper->add('parent', ModelType::class,
                [
                    'label' => 'config.label_parent',
                    'required' => false,
                    'btn_add' => false,
                    'placeholder' => 'config.label_select',
                ],
                [
                    'translation_domain' => 'PartitechSonataMenuBundle',
                ]
            );
        }




               $formMapper->add('classAttribute', TextType::class,
                    [
                        'label' => 'config.label_class_attribute',
                        'required' => false,
                    ],
                    [
                        'translation_domain' => 'PartitechSonataMenuBundle',
                    ]
                )
                ->add('enabled', null,
                    [
                        'label' => 'config.label_enabled',
                        'required' => false,
                    ],
                    [
                        'translation_domain' => 'PartitechSonataMenuBundle',
                    ]
                )
            ->end()

            ->with('config.label_menu_link', ['class' => 'col-md-6', 'translation_domain' => 'PartitechSonataMenuBundle'])
                ->add('menu', ModelType::class,
                    [
                        'label' => 'config.label_menu',
                        'required' => false,
                        'btn_add' => false,
                        'data' => $menu,
                        'placeholder' => 'config.label_select',
                    ],
                    [
                        'translation_domain' => 'PartitechSonataMenuBundle',
                    ]
                )
            ->end();

        if ($this->container->hasParameter('sonata.page.page.class')) {
            $subject = $this->getSubject();
            $url = $subject->getUrl();
            $pageClass = $this->container->getParameter('sonata.page.page.class');

            $em = $this->getModelManager()->getEntityManager($pageClass);
            $builder = $em->createQueryBuilder('p');

            $query = $builder->select('p.id, p.name, p.url')
                       ->from($pageClass, 'p')
                       ->getQuery();

            $pages = $query->getResult();

            $choices = [];

            foreach ($pages as $page) {
                $choices['config.label_select'] = null;
                $choices[$page['id'].' : '.ucfirst($page['name'])] = $page['url'];
            }
        }






        $formMapper->with('config.label_menu_link', ['class' => 'col-md-6', 'translation_domain' => 'PartitechSonataMenuBundle'])
                ->add('page', ChoiceType::class,
                    [
                        'label' => 'config.label_page',
                        'required' => false,
                        'choices' => $choices,
                        'data' => $url,
                        'empty_data' => null,
                    ],
                    [
                        'translation_domain' => 'PartitechSonataMenuBundle',
                    ]
                )
            ->end();


            $formMapper
                ->with('config.label_menu_link', ['class' => 'col-md-6', 'translation_domain' => 'PartitechSonataMenuBundle'])
                    ->add('url', TextType::class,
                        [
                            'label' => 'config.label_custom_url',
                            'required' => false,
                        ],
                        [
                            'translation_domain' => 'PartitechSonataMenuBundle',
                        ]
                    )
                    ->add('target', null,
                        [
                            'label' => 'config.label_target',
                            'required' => false,
                        ],
                        [
                            'translation_domain' => 'PartitechSonataMenuBundle',
                        ]
                    )
                ->end();
            $formMapper->end();
            $formMapper->tab('Seo')
            ->with('Parametres SEO', ['class' => 'col-md-12'])
                ->add('seo_title', TextType::class, [
                    'required' => false,
                    'label' => 'Title',
                    'data' => $this->getSubject()->getSeoTitle(),

                ])
                ->add('seo_description', TextareaType::class, [
                    'required' => false,
                    'label' => 'Description',
                    'data' => $this->getSubject()->getSeoDescription(),

                ])
                ->add('seo_index', ChoiceType::class, [
                    'choices' => $this->getSeoChoices(),
                    'required' => false,

                    'label' => 'Indexation',
                    'data' => $this->getSubject()->getSeoIndex(),
                ])
            ->end();
            $formMapper->end();
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->addIdentifier('name', null, ['label' => 'config.label_name', 'translation_domain' => 'PartitechSonataMenuBundle']);

        $listMapper->add('menu', null, [], EntityType::class,
            [
                'class' => $this->menuClass,
                'choice_label' => 'name',
            ]
        );

        $listMapper->add('_action', 'actions', [
            'label' => 'config.label_modify',
            'translation_domain' => 'PartitechSonataMenuBundle',
            'actions' => [
                'edit' => [],
                'delete' => [],
            ],
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('name')
            ->add('menu'
            );
    }

    public function prePersist(object $object): void
    {
        $this->rewriteUrl($object);
    }

    public function preUpdate(object $object): void
    {
        $this->rewriteUrl($object);
    }

    public function rewriteUrl($object)
    {
        if ($this->container->hasParameter('sonata.page.page.class')) {
            $data = $this->getForm()->get('page')->getData();
            if (!empty($data)) {
                $object->setUrl($data);
            }
        }
        $this->updateUrl($object);
    }

    /**
     * Update url.
     *
     * @param Menuitem $object
     */
    public function updateUrl($object)
    {
        $url = $object->getUrl();

        if (empty($url)) {
            $url = $this->slugify->slugify(strip_tags($object->getName()));

            if ($object->hasParent()) {
                $parent = $object->getParent();
                $url = $parent->getUrl().'MenuItemAdmin.php/'.$url;
            } else {
                $url = '/'.$url;
            }

            $object->setUrl($url);
        }
    }

    public function toString(object $object): string
    {
        return $object instanceof MenuItemInterface ? $object->getName() : $this->getTranslator()->trans('config.label_menu_item', [], 'PartitechSonataMenuBundle');
    }

    public function getSeoChoices()
    {

        return array(
            "index, follow" => "index, follow",
            "index, nofollow" => "index, nofollow",
            "noindex, follow" => "noindex, follow",
            "noindex, nofollow" => "noindex, nofollow",
        );
    }

}
