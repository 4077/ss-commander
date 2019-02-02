<?php namespace ss\commander\ui\controllers;

class Main extends \Controller
{
    private $s;

    public function __create()
    {
        $this->s = $this->s('|', [
            'sequence' => 'ab',
            'focus'    => 'a',
            'panels'   => [
                'a' => [
                    'tree_id'           => false,
                    'cat_id_by_tree_id' => []
                ],
                'b' => [
                    'tree_id'           => false,
                    'cat_id_by_tree_id' => []
                ]
            ]
        ]);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $s = $this->s;

        pusher()->subscribe();

        $sequence = $s['sequence'];

        $v->assign([
                       'L_PANEL_NAME' => $lPanelName = $sequence[0],
                       'L_PANEL'      => $this->c('panel~:view|' . $this->_instance() . '/panel-' . $lPanelName),
                       'R_PANEL_NAME' => $rPanelName = $sequence[1],
                       'R_PANEL'      => $this->c('panel~:view|' . $this->_instance() . '/panel-' . $rPanelName),
                   ]);

        $this->c('\std\ui\dialogs~:addContainer:ss/commander');

        $this->app->html->setFavicon(abs_url('-/ss/favicons/commander.png'));

        $this->css();

        $_w = [];
        for ($i = 0; $i < strlen($sequence); $i++) {
            $panel = substr($sequence, $i, 1);

            $_w[$panel] = $this->_w('panel~:|' . $this->_instance() . '/panel-' . $panel);
        }

        $this->widget(':|', [
            'panels' => [
                'focus' => ap($s, 'focus'),
                'list'  => array_keys($this->s['panels']),
            ],
            '.w'     => $_w,
            '.r'     => [
                'focus'     => $this->_p('>xhr:focus|'),
                'selectCat' => $this->_p('>xhr:selectCat|')
            ]
        ]);

        return $v;
    }
}
