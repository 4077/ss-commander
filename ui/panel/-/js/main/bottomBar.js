// head {
var __nodeId__ = "ss_commander_ui_panel__main_bottomBar";
var __nodeNs__ = "ss_commander_ui_panel";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.bind();
        },

        createPanelLoaded: false,

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $w.click(function () {
                w.w('content').unfocus();
                w.w('panel').focus('content');
            });

            // install

            var $installButton = $(".install.button", $w);

            $installButton.on("click", function (e) {
                w.install();

                e.stopPropagation();
            });

            $installButton.on("mouseenter", function () {
                w.w('opposite').highlight(w.w('opposite').getInstallTarget(), 'highlight target');
                w.w('content').highlight(w.w('content').getPreparedItems(['product']), 'highlight source')
            }).on("mouseleave", function () {
                w.w('opposite').unhighlight(w.w('opposite').getInstallTarget(), 'highlight target');
                w.w('content').unhighlight(w.w('content').getPreparedItems(['product']), 'highlight source')
            });

            // move

            var $moveButton = $(".move.button", $w);

            $moveButton.on("click", function (e) {
                w.move();

                e.stopPropagation();
            });

            $moveButton.on("mouseenter", function () {
                var moveTarget = w.w('opposite').getMoveTarget();
                var preparedItems = w.w('content').getPreparedItems(w.moveMap[moveTarget.type]);

                w.w('opposite').highlight(moveTarget, 'highlight target');
                w.w('content').highlight(preparedItems, 'highlight source');

            }).on("mouseleave", function () {
                var moveTarget = w.w('opposite').getMoveTarget();
                var preparedItems = w.w('content').getPreparedItems(w.moveMap[moveTarget.type]);

                w.w('opposite').unhighlight(moveTarget, 'highlight target');
                w.w('content').unhighlight(preparedItems, 'highlight source');
            });

            // copy

            var $copyButton = $(".copy.button", $w);

            $copyButton.on("click", function (e) {
                w.copy();

                e.stopPropagation();
            });

            $copyButton.on("mouseenter", function () {
                var copyTarget = w.w('opposite').getCopyTarget();
                var preparedItems = w.w('content').getPreparedItems(w.copyMap[copyTarget.type]);

                if (preparedItems.length) {
                    w.w('opposite').highlight(copyTarget, 'highlight target');
                    w.w('content').highlight(preparedItems, 'highlight source');
                }
            }).on("mouseleave", function () {
                var copyTarget = w.w('opposite').getCopyTarget();
                var preparedItems = w.w('content').getPreparedItems(w.copyMap[copyTarget.type]);

                w.w('opposite').unhighlight(copyTarget, 'highlight target');
                w.w('content').unhighlight(preparedItems, 'highlight source');
            });

            // edit

            var $editButton = $(".edit.button", $w);

            $editButton.on("click", function (e) {
                w.w('content').open();

                e.stopPropagation();
            });

            // delete

            var $deleteButton = $(".delete.button", $w);

            $deleteButton.on("click", function (e) {
                w.w('content').delete();

                e.stopPropagation();
            });

            // pluginsPanel

            var $pluginsPanelButton = $(".plugins_button", $w);

            $pluginsPanelButton.on("click", function () {
                w.r('togglePluginsPanel');
            });

            // create panel

            if (o.createPanelPinned) {
                w.createPanelLoaded = true;
            }

            var createPanelMouseLock = false;

            var $createPanelButton = $(".create_panel_button", $w);
            var $createPanel = $(".create_panel", $createPanelButton);

            $createPanel.on("click", function (e) {
                e.stopPropagation();
            });

            var createPanelHideTimeout = false;

            $createPanelButton.on("mouseenter", function () {
                if (!w.createPanelLoaded) {
                    w.r('loadCreatePanel');

                    w.createPanelLoaded = true;
                }

                $createPanel.show();

                clearTimeout(createPanelHideTimeout);
            }).on("mouseleave", function () {
                if (!o.createPanelPinned && !createPanelMouseLock) {
                    clearTimeout(createPanelHideTimeout);

                    createPanelHideTimeout = setTimeout(function () {
                        $createPanel.hide();
                    }, 400);
                }
            });

            var $pinButton = $(".pin", $createPanel);

            $pinButton.on("click", function (e) {
                createPanelMouseLock = true;

                w.r('toggleCreatePanel', {}, false, function (response) {
                    o.createPanelPinned = response.pinned;

                    $pinButton.toggleClass("pinned", response.pinned);

                    createPanelMouseLock = false;
                });

                e.stopPropagation();
            });

            //

            var $createProductButton = $(".create_product_button", $w);

            $createProductButton.on("click", function () {
                var target = w.w('content').getCreateProductTarget();

                if (target && target.type !== 'page') {
                    w.r('createProduct', {
                        target: target
                    });
                }
            });

            $createProductButton.on("mouseenter", function () {
                var target = w.w('content').getCreateProductTarget();

                if (target && target.type !== 'page') {
                    w.w('content').highlight(target, 'highlight target');
                }
            }).on("mouseleave", function () {
                var target = w.w('content').getCreateProductTarget();

                w.w('content').unhighlight(target, 'highlight target');
            });

            //

            var $createPageButton = $(".create_page_button", $w);

            $createPageButton.on("click", function () {
                var target = w.w('content').getCreatePageTarget();

                w.r('createPage', {
                    target: target
                });
            });

            $createPageButton.on("mouseenter", function () {
                var target = w.w('content').getCreatePageTarget();

                // if (target && target.type !== 'page') {
                w.w('content').highlight(target, 'highlight target');
                // }

}).on("mouseleave", function () {
                var target = w.w('content').getCreatePageTarget();

                w.w('content').unhighlight(target, 'highlight target');
            });
        },

        moveOrInstall: function () {
            if (this.options.split) {
                this.move();
            } else {
                this.install();
            }
        },

        install: function () {
            var w = this;

            var installTarget = w.w('opposite').getInstallTarget();

            if (installTarget) {
                var wContent = w.w('content');

                var items = wContent.getPreparedItems(['product']);

                w.r('install', {
                    target:    installTarget,
                    items:     items,
                    set_focus: wContent.options.focus
                }, false, false, true);
            }
        },

        moveMap: {
            page:      ['container', 'page'],
            container: ['product', 'page'],
            folder:    ['folder', 'product']
        },

        copyMap: {
            page:      ['container', 'page'],
            container: ['product', 'page'],
            folder:    ['folder']
        },

        move: function () {
            var w = this;

            var moveTarget = w.w('opposite').getMoveTarget();

            if (moveTarget) {
                var wContent = w.w('content');

                var items = wContent.getPreparedItems(this.moveMap[moveTarget.type]);

                if (!items.length) {
                    items = [wContent.options.focus];
                }

                w.r('move', {
                    target:    moveTarget,
                    items:     items,
                    set_focus: wContent.options.focus
                }, false, false, true);
            }
        },

        copy: function () {
            var w = this;

            var copyTarget = w.w('opposite').getCopyTarget();

            if (copyTarget) {
                var wContent = w.w('content');

                var items = wContent.getPreparedItems(this.copyMap[copyTarget.type]);

                if (!items.length) {
                    items = [wContent.options.focus];
                }

                w.r('copy', {
                    target:    copyTarget,
                    items:     items,
                    set_focus: wContent.options.focus
                }, false, false, true);
            }
        }
    });
})(__nodeNs__, __nodeId__);
