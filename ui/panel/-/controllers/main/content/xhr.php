<?php namespace ss\commander\ui\panel\controllers\main\content;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    public function __create()
    {
        $this->panel = commanderPanel($this->_instance());

        $this->tree = $this->panel->getTree();
        $this->cat = $this->panel->getCat();
    }

    public function reload()
    {
        $this->panel->reload();
    }

    public function focus()
    {
        $type = $this->data('type');
        $id = $this->data('id');

        $this->setFocus($type, $id);
    }

    private function setFocus($type, $id)
    {
        $tree = $this->tree;
        $panel = $this->panel;

        $sTree = &$this->s('@' . $tree->mode . '|' . $panel->instance . '/tree-' . $tree->id);

        $sTree['focus'] = [
            'type' => $type,
            'id'   => $id
        ];
    }

    public function select()
    {
        $type = $this->data('target/type');
        $id = $this->data('target/id');

        if (in($type, 'page, folder')) {
            if ($cat = \ss\models\Cat::find($id)) {
                $this->panel->setCat($cat);

                if ($id == $this->data('parent_cat_id')) {
                    $this->setFocus($type, $this->data('cat_id'));
                } else {
                    $this->setFocus($type, $cat->id);
                }

                $this->panel->reload();
            }
        }

        if ($type == 'product') {
            if ($product = \ss\models\Product::find($id)) {
                $oppositePanel = $this->panel->getOpposite();
                $oppositeTree = $oppositePanel->getTree();
                $oppositeTreeId = $oppositeTree->id;

                $tree = $this->tree;
                $panel = $this->panel;

                $cat = $product->cat;

                if ($cat->type == 'container') {
                    $cat = $cat->parent;
                }

                $panel->setCat($cat);

                $sTree = &$this->s('@' . $tree->mode . '|' . $panel->instance . '/tree-' . $tree->id);
                $oppositeSTree = &$this->s('@' . $oppositeTree->mode . '|' . $oppositePanel->instance . '/tree-' . $oppositeTree->id);

                $oppositeRelation = $panel->getOppositeRelation();

                if ($oppositeRelation == 'source') {
                    $oppositeProduct = \ss\models\Product::where('source_id', $id)->whereHas('cat', function ($query) use ($oppositeTreeId) {
                        $query->where('tree_id', $oppositeTreeId);
                    })->first();
                }

                if ($oppositeRelation == 'target') {
                    $oppositeProduct = \ss\models\Product::where('id', $product->source_id)->first();
                }

                if ($oppositeRelation == 'split') {
                    $oppositeProduct = $product;
                }

                $found = false;

                if (isset($oppositeProduct)) {
                    $oppositeCat = $oppositeProduct->cat;

                    if ($oppositeCat->type == 'container') {
                        $oppositeCat = $oppositeCat->parent;
                    }

                    $oppositePanel->setCat($oppositeCat);

                    ra($oppositeSTree, [
                        'focus'     => [
                            'type' => 'product',
                            'id'   => $oppositeProduct->id
                        ],
                        'selection' => [
                            'type' => 'product',
                            'id'   => $oppositeProduct->id
                        ]
                    ]);

                    $found = true;
                } else {
                    $oppositeSTree['selection'] = [
                        'type' => null,
                        'id'   => null
                    ];
                }

                ra($sTree, [
                    'focus'     => [
                        'type' => 'product',
                        'id'   => $product->id
                    ],
                    'selection' => [
                        'type' => 'product',
                        'id'   => $product->id
                    ]
                ]);

                if ($found) {

                } else {
                    $oppositeSTree['selection'] = [
                        'type' => null,
                        'id'   => null
                    ];

                    $sTree['selection'] = [
                        'type' => null,
                        'id'   => null
                    ];
                }

                $this->c('<~:reload|' . $this->panel->commander->instance);
            }
        }
    }

    public function scroll()
    {
        $tree = $this->tree;
        $panel = $this->panel;

        $sTree = &$this->s('@' . $tree->mode . '|' . $panel->instance . '/tree-' . $tree->id);

        ra($sTree, [
            'scroll' => [
                'left' => $this->data('left') ?: 0,
                'top'  => $this->data('top') ?: 0
            ]
        ]);
    }

    public function open()
    {
        $commanderInstance = $this->panel->commander->instance;

        $type = $this->data('type');
        $id = $this->data('id');

        if (in($type, 'page, container, folder')) {
            if ($cat = \ss\models\Cat::find($id)) {
                if (ss()->cats->isEditable($cat)) {
                    $this->c('\ss\cats\cp dialogs:' . $type . '|ss/commander', [
                        'cat' => $cat,
                        'ra'  => [
                            'callbacks' => [
                                'focus' => $this->_abs('<~app:disableKeyboard|' . $commanderInstance),
                                'close' => $this->_abs('<~app:enableKeyboard|' . $commanderInstance)
                            ]
                        ]
                    ]);
                }
            }
        }

        if ($type == 'product') {
            if ($product = \ss\models\Product::find($id)) {
                if (ss()->products->isEditable($product)) {
                    $this->c('\ss\cats\cp dialogs:' . $type . '|ss/commander', [
                        'product' => $product,
                        'ra'      => [
                            'callbacks' => [
                                'focus' => $this->_abs('<~app:disableKeyboard|' . $commanderInstance),
                                'close' => $this->_abs('<~app:enableKeyboard|' . $commanderInstance)
                            ]
                        ]
                    ]);
                }
            }
        }
    }

    public function delete()
    {
        $commanderInstance = $this->panel->commander->instance;

        $this->c('\std\ui\dialogs~:open:delete, ss|ss/commander', [
            'path'          => '@delete:view|',
            'data'          => [
                'items' => $this->data('items')
            ],
            'class'         => 'padding',
            //            'forgot_on_leave' => true,
            'callbacks'     => [
                'close' => $this->_abs('<~app:enableKeyboard|' . $commanderInstance)
            ],
            'pluginOptions' => [
                'title' => 'Удаление'
            ],
            'default'       => [
                'pluginOptions' =>
                    [
                        'width'  => 500,
                        'height' => 600,
                    ]
            ]
        ]);
    }

    public function toggleCollapse()
    {
        $panel = $this->panel;
        $tree = $this->tree;
        $cat = $this->panel->getCat();

        $sCat = &$this->s('@' . $tree->mode . '|' . $panel->commander->instance . '/cat-' . $cat->id);

        if ($this->data('collapsed')) {
            merge($sCat['collapsed_cats_ids'], (int)$this->data('container_id'));
        } else {
            diff($sCat['collapsed_cats_ids'], (int)$this->data('container_id'));
        }
    }

    public function togglePublished()
    {
        if ($this->a('ss:moderation')) {
            list($folders, $pages, $containers, $products) = $this->decompose();

            foreach ($folders as $folder) {
                $folder->published = !$folder->published;
                $folder->save();
            }

            foreach ($pages as $page) {
                $page->published = !$page->published;
                $page->save();

                pusher()->trigger('ss/page/update', [
                    'id'        => $page->id,
                    'published' => $page->published
                ]);
            }

            foreach ($containers as $container) {
                $container->published = !$container->published;
                $container->save();

//                pusher()->trigger('ss/tree/' . $cat->tree_id . '/page/any/enabled');

                pusher()->trigger('ss/container/update', [
                    'id'        => $container->id,
                    'published' => $container->published
                ]);

//                $this->se('ss/pages/any/toggle_enabled')->trigger();
            }

            foreach ($products as $product) {
                $updatedProducts = ss()->products->update($product, [
                    'published' => !$product->published
                ]);

                foreach ($updatedProducts as $updatedProduct) {
                    pusher()->trigger('ss/product/update', [
                        'id'        => $updatedProduct->id,
                        'published' => $product->published
                    ]);
                }
            }
        }
    }

    public function toggleEnabled()
    {
        if ($this->a('ss:moderation')) {
            list($folders, $pages, $containers, $products) = $this->decompose();

            foreach ($folders as $folder) {
                $folder->enabled = !$folder->enabled;
                $folder->save();
            }

            foreach ($pages as $page) {
                $page->enabled = !$page->enabled;
                $page->save();

                pusher()->trigger('ss/page/update', [
                    'id'      => $page->id,
                    'enabled' => $page->enabled
                ]);
            }

            foreach ($containers as $container) {
                $container->enabled = !$container->enabled;
                $container->save();

//                pusher()->trigger('ss/tree/' . $cat->tree_id . '/page/any/enabled');

                pusher()->trigger('ss/container/update', [
                    'id'      => $container->id,
                    'enabled' => $container->enabled
                ]);

//                $this->se('ss/pages/any/toggle_enabled')->trigger();
            }

            foreach ($products as $product) {
                $updatedProducts = ss()->products->update($product, [
                    'enabled' => !$product->enabled
                ]);

                foreach ($updatedProducts as $updatedProduct) {
                    pusher()->trigger('ss/product/update', [
                        'id'      => $updatedProduct->id,
                        'enabled' => $product->enabled
                    ]);
                }
            }
        }
    }

    public function arrangeFolders()
    {
        if ($placing = $this->data('placing')) {
            if (isset($placing['neighbor_id']) && isset($placing['side']) && in($placing['side'], 'before, after')) {
                $neighbor = \ss\models\Cat::find($placing['neighbor_id']);

                $delta = $placing['side'] == 'before' ? -5 : 5;

                if ($neighbor) {
                    $parent = $neighbor->parent;

                    $orderingField = 'position';

                    \ss\models\Cat::find($placing['id'])->update([
                                                                     $orderingField => $neighbor->{$orderingField} + $delta
                                                                 ]);

                    \DB::statement('SET @i := 0;');

                    $parent->nested()->orderBy($orderingField)->update([$orderingField => \DB::raw('(@i := @i + 10)')]);

                    pusher()->trigger('ss/cat/update_cats', [
                        'id' => $parent->id
                    ]);
                }
            }
        }
    }

    public function arrangePages()
    {
        if ($placing = $this->data('placing')) {
            if (isset($placing['neighbor_id']) && isset($placing['side']) && in($placing['side'], 'before, after')) {
                $neighbor = \ss\models\Cat::find($placing['neighbor_id']);

                if ($neighbor) {
                    $delta = $placing['side'] == 'before' ? -5 : 5;

                    $parent = $neighbor->parent;

                    $orderingField = 'position';

                    \ss\models\Cat::find($placing['id'])->update([
                                                                     $orderingField => $neighbor->{$orderingField} + $delta
                                                                 ]);

                    \DB::statement('SET @i := 0;');

                    if ($parent->type == 'container') {
                        $builder = $parent->containedPages();
                    } else {
                        $builder = $parent->nested();
                    }

                    $builder->orderBy($orderingField)->update([$orderingField => \DB::raw('(@i := @i + 10)')]);

                    pusher()->trigger('ss/cat/update_cats', [
                        'id' => $parent->id
                    ]);
                }
            }
        }
    }

    public function arrangeContainers()
    {
        if ($this->dataHas('sequence array')) {
            foreach ($this->data['sequence'] as $n => $id) {
                if (is_numeric($n) && $node = \ss\models\Cat::find($id)) {
                    $node->update(['position' => ($n + 1) * 10]);
                }
            }

            if (isset($node)) {
                pusher()->trigger('ss/cat/update_cats', [
                    'id' => $node->parent_id
                ]);
            }
        }
    }

    public function arrangeProducts()
    {
        if ($placing = $this->data('placing')) {
            if (isset($placing['neighbor_id']) && isset($placing['side']) && in($placing['side'], 'before, after')) {
                $neighbor = \ss\models\Product::find($placing['neighbor_id']);

                $delta = $placing['side'] == 'before' ? -5 : 5;

                if ($neighbor) {
                    $container = $neighbor->cat;

                    $orderingField = 'position';

                    \ss\models\Product::find($placing['id'])->update([
                                                                         $orderingField => $neighbor->{$orderingField} + $delta
                                                                     ]);

                    \DB::statement('SET @i := 0;');

                    $container->products()->orderBy($orderingField)->update([$orderingField => \DB::raw('(@i := @i + 10)')]);

                    pusher()->trigger('ss/cat/update_products', [
                        'id' => $container->id
                    ]);

                    pusher()->trigger('ss/cat/some_container_update_products', [
                        'id' => $container->parent_id
                    ]);
                }
            }
        }
    }

    private function decompose()
    {
        $items = $this->data('items');

        $foldersIds = [];
        $pagesIds = [];
        $containersIds = [];
        $productsIds = [];

        foreach ($items as $item) {
            $type = $item['type'];
            $id = $item['id'];

            if ($type == 'folder') {
                $foldersIds[] = $id;
            }

            if ($type == 'page') {
                $pagesIds[] = $id;
            }

            if ($type == 'container') {
                $containersIds[] = $id;
            }

            if ($type == 'product') {
                $productsIds[] = $id;
            }
        }

        $folders = \ss\models\Cat::whereIn('id', $foldersIds)->get();
        $pages = \ss\models\Cat::whereIn('id', $pagesIds)->get();
        $containers = \ss\models\Cat::whereIn('id', $containersIds)->get();
        $products = \ss\models\Product::whereIn('id', $productsIds)->get();

        return [$folders, $pages, $containers, $products];
    }
}
