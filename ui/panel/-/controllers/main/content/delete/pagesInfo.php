<?php namespace ss\commander\ui\panel\controllers\main\content\delete;

class PagesInfo extends \Controller
{
    public function view()
    {
        $v = $this->v();

        $pages = $this->data['pages'];

        foreach ($pages as $page) {
            $deleteInfo = ss()->cats->getDeleteInfo($page);

            foreach ($deleteInfo['tree'] as $row) {
                $cat = $row['cat'];

                $isContainer = $cat->type == 'container';

                $v->assign('cat/prediction_row', [
                    'TYPE_CLASS'   => $isContainer ? 'container' : 'page',
                    'ICON_CLASS'   => $isContainer ? 'fa fa-cube' : 'fa fa-file',
                    'INDENT_WIDTH' => $row['level'] * 16,
                    'NAME'         => ss()->cats->getShortName($row['cat'])
                ]);

                if ($denied = $row['denied']) {
                    $v->append('cat/prediction_row', [
                        'DELETING_CLASS' => 'not_deleting',
                        'INFO'           => $isContainer ? 'не будет удален' : 'не будет удалена'
                    ]);

                    $deniedReason = $row['denied_by_access']
                        ? 'нет доступа на удаление' : ($row['denied_by_nested']
                            ? 'нет доступа на удаление одной из вложенных страниц или контейнеров' : ($row['denied_by_products']
                                ? 'нет доступа на удаление товаров в этом контейнере' : ''));

                    $v->assign('cat/prediction_row/reason', [
                        'CONTENT' => $deniedReason
                    ]);
                } else {
                    $info = $isContainer ? 'будет удален' : 'будет удалена';

                    if ($products = $row['products']) {
                        $productsCount = count($products);

                        $info .= ' вместе с ' . $productsCount . ' товар' . ending($productsCount, 'ом', 'ами', 'ами');

                        /**
                         * @var $refsInfo \ss\Svc\Products\RefsInfo
                         */
                        if ($refsInfo = $row['refs_info']) {
                            if ($refsIds = $refsInfo->getRefsIds()) {
                                $refsCount = count($refsIds);

                                $info .= '<b> и ' . $refsCount . ' ссылающим' . ending($refsCount, 'ся', 'ися', 'ися') . ' товар' . ending($refsCount, 'ом', 'ами', 'ами') . ' в других ветках</b>';
                            }
                        }
                    }

                    $v->append('cat/prediction_row', [
                        'DELETING_CLASS' => 'deleting',
                        'INFO'           => $info
                    ]);
                }
            }
        }

        $this->css();

        return $v;
    }
}
