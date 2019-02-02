// head {
var __nodeId__ = "ss_commander_ui_panel__main_topBar";
var __nodeNs__ = "ss_commander_ui_panel";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $w.click(function () {
                w.w('content').unfocus();
                w.w('panel').focus('content');
            });

            $("> .main > .nav > .branch .node", $w).on("click", function () {
                w.r('select', {
                    cat_id:        o.catId,
                    parent_cat_id: o.parentId,
                    target:        {
                        type: o.treeMode === 'pages' ? 'page' : 'folder',
                        id:   $(this).attr("cat_id")
                    }
                });
            });

            $("> .main > .nav > .branch .node", $w).on("contextmenu", function (e) {
                w.r('open', {
                    type: o.treeMode === 'pages' ? 'page' : 'folder',
                    id:   $(this).attr("cat_id")
                });

                e.preventDefault();
            });

            $(".force_button", $w).on("click", function (e) {
                var $button = $(this);

                var mode = $button.attr("mode");

                if (o.forceCollapseMode === mode) {
                    o.forceCollapseMode = false;
                } else {
                    o.forceCollapseMode = mode;
                }

                w.w('content').options.forceCollapseMode = o.forceCollapseMode;

                w.renderForceCollapseMode();
                w.updateForceCollapseMode();

                e.stopPropagation()
            });
        },

        rootFocused: function (value) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $(".root_indicator", $w).toggleClass("focus", value);
        },

        renderForceCollapseMode: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $(".force_button", $w).removeClass("pressed").filter("[mode='" + o.forceCollapseMode + "']").addClass("pressed");

            w.w('content').setForceCollapseMode(o.forceCollapseMode);
        },

        updateForceCollapseMode: function () {
            var w = this;
            var o = w.options;

            w.mr('updateForceCollapseMode', {
                mode: o.forceCollapseMode
            });
        }
    });
})(__nodeNs__, __nodeId__);
