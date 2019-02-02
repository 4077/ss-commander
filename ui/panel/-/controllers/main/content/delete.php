<?php namespace ss\commander\ui\panel\controllers\main\content;

class Delete extends \Controller
{
    public function __create()
    {
        $this->dmap('|', 'items');
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        list($folders, $pages, $containers, $products) = $this->decompose();

        if ($folders) {
            $v->assign('folders', [
                'CONTENT' => $this->c('>foldersInfo:view', [
                    'folders' => $folders
                ])
            ]);

            $v->assign([
                           'DELETE_FOLDERS_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:deleteFolders|',
                               'data'    => [
                                   'ids' => j64_(array_keys($folders))
                               ],
                               'class'   => 'button delete',
                               'content' => 'Удалить'
                           ]),
                           'CANCEL_FOLDERS_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:cancelFolders|',
                               'class'   => 'button cancel',
                               'content' => 'Отмена'
                           ]),
                       ]);
        }

        if ($pages) {
            $v->assign('pages', [
                'CONTENT' => $this->c('>pagesInfo:view', [
                    'pages' => $pages
                ])
            ]);

            $v->assign([
                           'DELETE_PAGES_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:deletePages|',
                               'data'    => [
                                   'ids' => j64_(array_keys($pages))
                               ],
                               'class'   => 'button delete',
                               'content' => 'Удалить'
                           ]),
                           'CANCEL_PAGES_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:cancelPages|',
                               'class'   => 'button cancel',
                               'content' => 'Отмена'
                           ]),
                       ]);
        }

        if ($containers) {
            $v->assign('containers', [
                'CONTENT' => $this->c('>containersInfo:view', [
                    'containers' => $containers
                ])
            ]);

            $v->assign([
                           'DELETE_CONTAINERS_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:deleteContainers|',
                               'data'    => [
                                   'ids' => j64_(array_keys($containers))
                               ],
                               'class'   => 'button delete',
                               'content' => 'Удалить'
                           ]),
                           'CANCEL_CONTAINERS_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:cancelContainers|',
                               'class'   => 'button cancel',
                               'content' => 'Отмена'
                           ]),
                       ]);
        }

        if ($products) {
            $v->assign('products', [
                'CONTENT' => $this->c('>productsInfo:view', [
                    'products' => $products
                ])
            ]);

            $v->assign([
                           'DELETE_PRODUCTS_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:deleteProducts|',
                               'data'    => [
                                   'ids' => j64_(array_keys($products))
                               ],
                               'class'   => 'button delete',
                               'content' => 'Удалить'
                           ]),
                           'CANCEL_PRODUCTS_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:cancelProducts|',
                               'class'   => 'button cancel',
                               'content' => 'Отмена'
                           ]),
                       ]);
        }

        $this->css(':\css\std~');

        return $v;
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

        $folders = map(table_rows_by_id(\ss\models\Cat::whereIn('id', $foldersIds)->get()), $foldersIds);
        $pages = map(table_rows_by_id(\ss\models\Cat::whereIn('id', $pagesIds)->get()), $pagesIds);
        $containers = map(table_rows_by_id(\ss\models\Cat::whereIn('id', $containersIds)->get()), $containersIds);
        $products = map(table_rows_by_id(\ss\models\Product::whereIn('id', $productsIds)->get()), $productsIds);

        if (!$folders && !$pages && !$containers && !$products) {
            $this->c('\std\ui\dialogs~:close:delete|ss/commander');
        }

        return [$folders, $pages, $containers, $products];
    }
}
