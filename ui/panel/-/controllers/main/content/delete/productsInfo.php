<?php namespace ss\commander\ui\panel\controllers\main\content\delete;

class ProductsInfo extends \Controller
{
    public function view()
    {
        $v = $this->v();

        $products = $this->data['products'];

        foreach ($products as $product) {
            $denied = !ss()->products->isCDable($product);

            if ($denied) {
                $info = 'не будет удален';
            } else {
                $info = 'будет удален';

                $refsCount = $product->refs()->count();

                if ($refsCount) {
                    $info .= ' вместе с <b>' . $refsCount . ending(
                            $refsCount,
                            ' ссылающимся товаром в другой ветке',
                            ' ссылающимися товарами в других ветках',
                            ' ссылающимися товарами в других ветках'
                        ) . '</b>';
                }
            }

            $v->assign('product', [
                'NAME'           => $product->name,
                'INFO'           => $info,
                'DELETING_CLASS' => $denied ? 'not_deleting' : 'deleting'
            ]);

            if ($denied) {
                $cat = $product->cat;

                $v->assign('product/reason', [
                    'CONTENT' => 'нет прав на удаление товаров в '
                        . ($cat->type === 'container' ? 'контейнере' : 'папке') . ' '
                        . ss()->cats->getShortName($cat)
                ]);
            }
        }

        $this->css();

        return $v;
    }
}
