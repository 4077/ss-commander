<?php namespace ss\commander\ui\panel\controllers\main\content\delete;

class FoldersInfo extends \Controller
{
    public function view()
    {
        $v = $this->v();

        $folders = $this->data['folders'];

        foreach ($folders as $folder) {
            $deleteInfo = ss()->cats->getDeleteInfo($folder);

            foreach ($deleteInfo['tree'] as $row) {
                $v->assign('folder/prediction_row', [
                    'INDENT_WIDTH' => $row['level'] * 16,
                    'NAME'         => ss()->cats->getShortName($row['cat'])
                ]);

                if ($denied = $row['denied']) {
                    $v->append('folder/prediction_row', [
                        'DELETING_CLASS' => 'not_deleting',
                        'INFO'           => 'не будет удалена'
                    ]);

                    $deniedReason = $row['denied_by_access']
                        ? 'нет доступа на удаление' : ($row['denied_by_nested']
                            ? 'нет доступа на удаление одной из вложенных папок' : ($row['denied_by_products']
                                ? 'нет доступа на удаление товаров в этой папке' : ''));

                    $v->assign('folder/prediction_row/reason', [
                        'CONTENT' => $deniedReason
                    ]);
                } else {
                    $info = 'будет удалена';

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

                    $v->append('folder/prediction_row', [
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
