// head {
var __nodeId__ = "ss_commander_ui_panel__main_content_pages";
var __nodeNs__ = "ss_commander_ui_panel";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {
            focus: {
                type: null,
                id:   null
            }
        },

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.render();
            w.bind();
            w.bindEvents();
        },

        render: function () {
            this.renderScroll();
            this.renderFocus();
            this.renderSelection();
        },

        numerateRows: function ($rows) {
            $rows = $rows || $(".row", this.element);

            $rows.attr("n", "none");

            $rows.filter(":visible").attr("n", function (n) {
                return n;
            });
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $w.scroll(function () {
                w.updateScroll();
            });

            $w.on("click", function () {
                w.unfocus();
                w.w('panel').focus('content');
            });

            $(".estop", $w).on("click", function (e) {
                e.stopPropagation();
            });

            var rowClickTimeout;

            var $rows = $(".row", $w);

            w.numerateRows($rows);

            $rows.on("click", function (e) {
                var $row = $(this);

                clearTimeout(rowClickTimeout);

                rowClickTimeout = setTimeout(function () {
                    w.setFocus($row, true);
                    w.updateFocus();
                }, 100);

                e.stopPropagation();
            });

            $rows.on("dblclick", function () {
                clearTimeout(rowClickTimeout);

                w.setFocus($(this));
                w.select();
            });

            $rows.on("contextmenu", function (e) {
                w.setFocus($(this));
                w.open();

                e.preventDefault();
            });

            $rows.filter(".container").each(function () {
                var $containerTable = $(this).closest("table");

                // $(".toggle_listener", $containerTable).on("click", function () {
                $(".container.row", $containerTable).on("click", function () {
                    w.toggleCollapse($containerTable);
                });
            });

            $w.on("contextmenu", function (e) {
                e.preventDefault();
            });

            if (o.sortable) {
                $("table.pages", $w).each(function () {
                    var $table = $(this);

                    $table.sortable({
                        items:    ".row.page.sortable",
                        axis:     'y',
                        distance: 8,
                        helper:   function (e, tr) { // http://stackoverflow.com/questions/1307705/jquery-ui-sortable-with-table-and-tr-width
                            var $originals = tr.children();
                            var $helper = tr.clone();

                            $helper.children().each(function (index) {
                                $(this).width($originals.eq(index).width()); // Set helper cell sizes to match the original sizes
                            });

                            return $helper;
                        },
                        start:    function (e, ui) {
                            ui.placeholder.height(ui.item.height());
                        },
                        update:   function (e, ui) {
                            var previous = false;
                            var placedBeforeNext = false;

                            var placingData = {
                                id:          ui.item.attr("row_id"),
                                neighbor_id: false,
                                side:        false
                            };

                            $(".page.row.sortable", $table).each(function () {
                                if (placedBeforeNext) {
                                    placingData.neighbor_id = $(this).attr("row_id");
                                    placingData.side = 'before';

                                    placedBeforeNext = false;
                                }

                                if ($(this).attr("row_id") === ui.item.attr("row_id")) {
                                    if (previous) {
                                        placingData.neighbor_id = previous;
                                        placingData.side = 'after';
                                    } else {
                                        placedBeforeNext = true;
                                    }
                                }

                                previous = $(this).attr("row_id");
                            });

                            w.r('arrangePages', {
                                placing: placingData
                            });

                            e.stopPropagation();
                        }
                    });
                });

                $("table[container_id]", $w).each(function () {
                    var $container = $(this);

                    $container.sortable({
                        items:    ".row.product",
                        axis:     'y',
                        distance: 8,
                        helper:   function (e, tr) { // http://stackoverflow.com/questions/1307705/jquery-ui-sortable-with-table-and-tr-width
                            var $originals = tr.children();
                            var $helper = tr.clone();

                            $helper.children().each(function (index) {
                                $(this).width($originals.eq(index).width()); // Set helper cell sizes to match the original sizes
                            });

                            return $helper;
                        },
                        start:    function (e, ui) {
                            ui.placeholder.height(ui.item.height());
                        },
                        update:   function (e, ui) {
                            var previous = false;
                            var placedBeforeNext = false;

                            var placingData = {
                                id:          ui.item.attr("row_id"),
                                neighbor_id: false,
                                side:        false
                            };

                            $(".product.row", $container).each(function () {
                                if (placedBeforeNext) {
                                    placingData.neighbor_id = $(this).attr("row_id");
                                    placingData.side = 'before';

                                    placedBeforeNext = false;
                                }

                                if ($(this).attr("row_id") === ui.item.attr("row_id")) {
                                    if (previous) {
                                        placingData.neighbor_id = previous;
                                        placingData.side = 'after';
                                    } else {
                                        placedBeforeNext = true;
                                    }
                                }

                                previous = $(this).attr("row_id");
                            });

                            w.r('arrangeProducts', {
                                placing: placingData
                            });

                            e.stopPropagation();
                        }
                    });
                });
            }
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/cat/update_cats.' + o.panelName, function (data) {
                if (o.catId === data.id) {
                    w.mr('reload');
                }
            });

            w.e('ss/cat/some_container_update_products.' + o.panelName, function (data) {
                if (o.catId === data.id) {
                    w.mr('reload');
                }
            });

            w.e('ss/page/update.' + o.panelName, function (data) {
                var $page = $(".page[row_id='" + data.id + "']", $w);

                if ($page.length) {
                    if (isset(data.enabled)) {
                        $page.toggleClass("disabled", !data.enabled);
                    }

                    if (isset(data.published)) {
                        $page.toggleClass("not_published", !data.published);
                    }

                    if (isset(data.name)) {
                        $("> .name .value", $page).html(data.name);
                    }
                }
            });

            w.e('ss/container/update.' + o.panelName, function (data) {
                var $container = $(".container[row_id='" + data.id + "']", $w);

                if ($container.length) {
                    if (isset(data.enabled)) {
                        $container.toggleClass("disabled", !data.enabled);
                    }

                    if (isset(data.published)) {
                        $container.toggleClass("not_published", !data.published);
                    }

                    if (isset(data.name)) {
                        $("> .bar > .wrapper > .name .value", $container).html(data.shortName);
                    }
                }
            });

            w.e('ss/product/update.' + o.panelName, function (data) {
                var $product = $(".product[row_id='" + data.id + "']", $w);

                if ($product.length) {
                    if (isset(data.enabled)) {
                        $product.toggleClass("disabled", !data.enabled);
                    }

                    if (isset(data.published)) {
                        $product.toggleClass("not_published", !data.published);
                    }

                    if (isset(data.name)) {
                        $("> .name .value", $product).html(data.name);
                    }

                    if (isset(data.price)) {
                        $("> .price", $product).html(data.formatted);
                    }

                    if (isset(data.stock)) {
                        $("> .stock", $product).html(data.formatted); // todo ???
                    }

                    if (isset(data.images)) {
                        $(".has_image.icon", $product).toggleClass("has", data.images.has).attr("title", data.images.count);
                    }

                    if (isset(data.status)) {
                        var $status = $(".status.icon", $product);

                        for (var status in o.statuses) {
                            $status.removeClass(status).find("div").removeClass(o.statuses[status].icon);
                        }

                        $status.addClass(data.status).find("div").addClass(o.statuses[data.status].icon);
                    }
                }
            });
        },

        handleKeyboardEvent: function (e) {
            var which = e.which;
            var type = e.type;
            var prevent = false;

            if (type === 'keydown' && which === 38) { // arrow up
                this.moveFocus(-1);
                this.updateFocus();

                prevent = true;
            }

            if (type === 'keydown' && which === 40) { // arrow down
                this.moveFocus(1);
                this.updateFocus();

                prevent = true;
            }

            if (type === 'keydown' && which === 27) { // esc
                var $prepared = $(".row.prepared", this.element);

                if ($prepared.length) {
                    $(".row.prepared", this.element).removeClass("prepared");
                } else {
                    this.unfocus();
                    this.deselect();
                }

                prevent = true;
            }

            if (type === 'keydown' && which === 13) { // enter
                this.select();

                prevent = true;
            }

            if (type === 'keydown' && (which === 45 || which === 83)) { // insert || s
                this.togglePrepared();

                prevent = true;
            }

            if (type === 'keyup' && which === 46) { // delete
                this.delete();

                prevent = true;
            }

            if (type === 'keyup' && which === 70) { // f
                var $containerTable = this.getFocusedContainerTable();

                if ($containerTable) {
                    this.toggleCollapse($containerTable);
                }

                prevent = true;
            }

            if (type === 'keyup' && which === 80) { // p
                this.togglePublished();

                prevent = true;
            }

            if (type === 'keyup' && which === 69) { // e
                this.toggleEnabled();

                prevent = true;
            }

            if (type === 'keydown' && which === 117) { // F6
                e.preventDefault();
            }

            if (type === 'keyup' && (which === 107 || which === 117)) { // + || F6
                this.w('panel').w('bottomBar').moveOrInstall();

                prevent = true;
            }

            if (type === 'keydown' && which === 32) { // space
                this.open();

                prevent = true;
            }

            if (prevent) {
                e.preventDefault();
            }
        },

        /**
         * FOCUS
         */

        unfocus: function () {
            this.unsetFocus();
            this.updateFocus();
        },

        setFocus: function ($row, panelFocus) {
            this.options.focus.type = $row.attr("type");
            this.options.focus.id = parseInt($row.attr("row_id"));

            this.renderFocus($row);

            if (panelFocus) {
                this.w('panel').focus('content');
            }

            this.w('panel').rootFocused(this.options.focus.id === this.options.catId);
        },

        unsetFocus: function () {
            this.options.focus.type = 'page';
            this.options.focus.id = this.options.catId;

            this.renderFocus();

            this.w('panel').rootFocused(true);
        },

        renderFocus: function ($row) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $row = $row || $(".row[type='" + o.focus.type + "'][row_id='" + o.focus.id + "']", $w);

            $(".row", $w).removeClass("focus");

            if ($row.length) {
                $row.addClass("focus");

                setTimeout(function () {
                    w.scrollToRow($row, false);
                });
            } else {
                setTimeout(function () {
                    o.focus.type = 'page';
                    o.focus.id = o.catId;

                    w.w('panel').rootFocused(true);
                });
            }
        },

        updateFocusTimeout: 0,

        updateFocus: function () {
            var w = this;
            var o = w.options;

            clearTimeout(this.updateFocusTimeout);
            this.updateFocusTimeout = setTimeout(function () {
                w.mr('focus', o.focus);
            }, 100);
        },

        moveFocus: function (delta) {
            var n = parseInt($(".row[row_id='" + this.options.focus.id + "']", this.element).attr("n"));

            n += delta;

            var $row = $(".row[n='" + n + "']", this.element);

            if ($row.length) {
                this.setFocus($row);
            }
        },

        /**
         * FIND
         */

        find: function (target) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $row = $(".row[type='" + target.type + "'][row_id='" + target.id + "']", $w);

            if (!$row.length) {

            }
        },

        /**
         * SELECTION
         */

        select: function () {
            var w = this;
            var o = this.options;

            var target = o.focus;

            if (o.focus.type === 'page' && o.focus.id === o.catId) {
                target = {
                    type: 'page',
                    id:   o.parentId
                }
            }

            w.r('select', {
                cat_id:        o.catId,
                parent_cat_id: o.parentId,
                target:        target
            });
        },

        deselect: function () {
            this.options.selection.type = null;
            this.options.selection.id = null;

            this.renderSelection();
        },

        renderSelection: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $(".row", $w).removeClass("selected");

            var $row = $(".row[type='" + o.selection.type + "'][row_id='" + o.selection.id + "']", $w);

            if ($row.length) {
                $row.addClass("selected");

                if (this.options.selection.type === 'product') {
                    var $containerTable = $row.closest(".container_table");

                    if (o.forceCollapseMode === 'collapse') {
                        $containerTable.addClass("temporary_expand");
                    } else {
                        $(".container_table", $w).removeClass("temporary_expand");

                        $containerTable.removeClass("collapsed");

                        var collapsed = $containerTable.hasClass("collapsed");

                        var $indicator = $(".products_count .icon", $containerTable);

                        $indicator.toggleClass("fa-caret-left", collapsed);
                        $indicator.toggleClass("fa-caret-down", !collapsed);
                    }

                    w.numerateRows();
                }
            }
        },

        /**
         * TARGETS
         */

        getInstallTarget: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            if (o.focus.type === 'container') {
                return o.focus;
            }

            if (o.focus.type === 'product') {
                var $row = $(".row[type='product'][row_id='" + o.focus.id + "']", $w);

                return {
                    type: 'container',
                    id:   $row.attr("container_id")
                };
            }
        },

        getCreateProductTarget: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            if (o.focus.type === 'container') {
                return o.focus;
            }

            if (o.focus.type === 'product') {
                var $row = $(".row[type='product'][row_id='" + o.focus.id + "']", $w);

                return {
                    type: 'container',
                    id:   $row.attr("container_id")
                };
            }
        },

        getCreatePageTarget: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            if (o.focus.type === 'container') {
                return o.focus;
            }

            if (o.focus.type === 'product') {
                var $row = $(".row[type='product'][row_id='" + o.focus.id + "']", $w);

                return {
                    type: 'container',
                    id:   $row.attr("container_id")
                };
            }

            if (o.focus.type === 'page') {
                if (o.focus.id === o.catId) {
                    return o.focus;
                } else {
                    var $row = $(".row[type='page'][row_id='" + o.focus.id + "']", $w);

                    return {
                        type: 'container',
                        id:   $row.attr("container_id")
                    };
                }
            }
        },

        getMoveTarget: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            if (o.focus.type === 'page') {
                if (parseInt(o.focus.id) === parseInt(o.catId)) { // ?
                    return {
                        type: 'page',
                        id:   o.catId
                    };
                } else {
                    return o.focus;
                }
            }

            if (o.focus.type === 'container') {
                return o.focus;
            }

            if (o.focus.type === 'product') {
                var $row = $(".row[type='product'][row_id='" + o.focus.id + "']", $w);

                return {
                    type: 'container',
                    id:   $row.attr("container_id")
                };
            }
        },

        getCopyTarget: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            if (o.focus.type === 'page') {
                if (parseInt(o.focus.id) === parseInt(o.catId)) { // ?
                    return {
                        type: 'page',
                        id:   o.catId
                    };
                } else {
                    return o.focus;
                }
            }

            if (o.focus.type === 'container') {
                return o.focus;
            }

            /*if (o.focus.type === 'product') {
                var $row = $(".row[type='product'][row_id='" + o.focus.id + "']", $w);

                return {
                    type: 'container',
                    id:   $row.attr("container_id")
                };
            }*/
        },

        getPreparedItems: function (typesFilter) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var items = [];

            $(".row.prepared", $w).each(function () {
                var type = $(this).attr("type");

                if (!typesFilter || typesFilter.indexOf(type) !== -1) {
                    items.push({
                        type: type,
                        id:   $(this).attr("row_id")
                    });
                }
            });

            if (!items.length) {
                if (!typesFilter || typesFilter.indexOf(o.focus.type) !== -1) {
                    items = [o.focus];
                }
            }

            return items;
        },

        getFocusedContainerTable: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $row;

            if (o.focus.type === 'page') {
                $row = $(".row[type='page'][row_id='" + o.focus.id + "']", $w);

                return $row.closest(".container_table");
            }


            if (o.focus.type === 'container') {
                $row = $(".row[type='container'][row_id='" + o.focus.id + "']", $w);

                return $row.closest(".container_table");
            }

            if (o.focus.type === 'product') {
                $row = $(".row[type='product'][row_id='" + o.focus.id + "']", $w);

                return $row.closest(".container_table");
            }
        },

        /**
         * HIGHLIGHTING
         */

        highlight: function (items, classes) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            if (o.focus.type === 'page' && o.focus.id === o.catId) {
                $w.closest(".content").addClass(classes);
            } else {
                this._walkRows(items, function ($row) {
                    $row.addClass(classes);
                });
            }
        },

        unhighlight: function (items, classes) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            if (o.focus.type === 'page' && o.focus.id === o.catId) {
                $w.closest(".content").removeClass(classes);
            } else {
                this._walkRows(items, function ($row) {
                    $row.removeClass(classes);
                });
            }
        },

        /**
         * SCROLL
         */

        scrollToRow: function ($row, animate) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $table = $("> table", $w);

            var padding = 0; // hardcode padding

            var portHeight = $w.height();
            var tableTop = $table.position().top - padding;
            var rowTop = $row.position().top - tableTop;

            var minTop = -$table.position().top + padding;
            var maxTop = -$table.position().top + portHeight + padding;

            if (rowTop > maxTop) {
                if (animate) {
                    $w.stop().animate({
                        scrollTop: rowTop - portHeight + $row.height() * 3
                    }, 300, 'swing');
                } else {
                    $w.scrollTop(rowTop - portHeight + $row.height());
                }
            }

            if (rowTop < minTop) {
                if (animate) {
                    $w.stop().animate({
                        scrollTop: rowTop - $row.height() * 3
                    }, 300, 'swing');
                } else {
                    $w.scrollTop(rowTop);
                }
            }
        },

        renderScroll: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $w.scrollLeft(o.scroll.left);
            $w.scrollTop(o.scroll.top);
        },

        updateScrollTimeout: 0,

        updateScroll: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            clearTimeout(this.updateScrollTimeout);

            this.updateScrollTimeout = setTimeout(function () {
                w.mr('scroll', {
                    left: $w.scrollLeft(),
                    top:  $w.scrollTop()
                });
            }, 100);
        },

        /**
         * ACTIONS
         */

        open: function () {
            var w = this;
            var o = w.options;

            w.r('open', o.focus);
        },

        delete: function () {
            var w = this;
            var o = w.options;

            if (o.editable) {
                var items = w.getPreparedItems();

                if (items.length) {
                    w.r('delete', {
                        items:     items,
                        set_focus: o.focus // ?
                    });
                }
            }
        },

        /**
         * TOGGLES
         */

        togglePublished: function () {
            this.r('togglePublished', {
                items: this.getPreparedItems()
            });

            this.moveFocus(1);
        },

        toggleEnabled: function () {
            this.r('toggleEnabled', {
                items: this.getPreparedItems()
            });

            this.moveFocus(1);
        },

        togglePrepared: function () {
            var focus = this.options.focus;

            var $row = $(".row[type='" + focus.type + "'][row_id='" + focus.id + "']", this.element);

            $row.toggleClass("prepared");

            this.moveFocus(1);
        },

        /**
         * CONTAINERS COLLAPSE
         */

        toggleCollapse: function ($containerTable) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            if (o.forceCollapseMode === 'collapse') {
                if ($containerTable.hasClass("temporary_expand")) {
                    $containerTable.removeClass("temporary_expand");
                } else {
                    $(".container_table", $w).removeClass("temporary_expand");
                    $containerTable.addClass("temporary_expand");
                }

                w.setFocus($(".container.row", $containerTable));

                w.numerateRows();
                setTimeout(function () {
                    w.renderFocus();
                });

            } else {
                $(".container_table", $w).removeClass("temporary_expand");

                $containerTable.toggleClass("collapsed");

                var collapsed = $containerTable.hasClass("collapsed");

                var $indicator = $(".products_count .icon", $containerTable);

                $indicator.toggleClass("fa-caret-left", collapsed);
                $indicator.toggleClass("fa-caret-down", !collapsed);

                if (collapsed) {
                    w.setFocus($(".container.row", $containerTable));
                }

                w.numerateRows();
                setTimeout(function () {
                    w.renderFocus();
                });

                w.r('toggleCollapse', {
                    container_id: $containerTable.attr("container_id"),
                    collapsed:    collapsed
                });
            }
        },

        setForceCollapseMode: function (mode) {
            var w = this;
            var $w = w.element;

            $("table.container_table", $w).removeClass("force_collapse").removeClass("force_expand");

            if (mode !== false) {
                $("table.container_table", $w).addClass("force_" + mode);
            }

            w.numerateRows();
        },

        // OTHER

        _walkRows: function (items, handler) {
            var $w = this.element;

            if (!(items instanceof Array)) {
                items = [items];
            }

            $.each(items, function () {
                var $row = $(".row[type='" + this.type + "'][row_id='" + this.id + "']", $w);

                handler($row);
            });
        }
    });
})(__nodeNs__, __nodeId__);
