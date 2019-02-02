// head {
var __nodeId__ = "ss_commander2_ui_panel__main_bottomBar_createPanel_treeSelector";
var __nodeNs__ = "ss_commander2_ui_panel";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var dropdownLoaded = false;

            $w.bind("click", function (e) {
                if (!dropdownLoaded) {
                    w.r('loadDropdown');
                }

                $(".dropdown", $w).toggle();

                e.stopPropagation();
            });

            $(window).rebind("click." + __nodeId__ + '-panel-' + o.panelName, function () {
                $(".dropdown", $w).hide();
            });
        }
    });
})(__nodeNs__, __nodeId__);
