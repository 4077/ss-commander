<table class="container_table {__NODE_ID__} {COLLAPSED_CLASS} {FORCE_COLLAPSE_MODE_CLASS}" instance="{__INSTANCE__}" container_id="{ID}">
    <tr class="container row {DISABLED_CLASS} {NOT_PUBLISHED_CLASS} {LOCKED_CLASS}" type="container" row_id="{ID}" title="{TITLE}">
        <td class="icon toggle_listener">
            <div class="fa fa-cube"></div>
        </td>
        <td class="bar">
            <div class="wrapper">
                <div class="name">
                    <span class="value">{NAME}</span><span class="locked_icon fa fa-lock"></span>
                </div>

                <div class="products_count toggle_listener">
                    <div class="value">{PRODUCTS_COUNT}</div>
                    <div class="icon fa {PRODUCT_COUNT_ICON_CLASS}"></div>
                </div>
            </div>
        </td>
    </tr>
    <tr class="content">
        <td class="indent"></td>
        <td>
            <table class="pages">
                <!-- page -->
                <tr class="page row sortable {DISABLED_CLASS} {NOT_PUBLISHED_CLASS} {LOCKED_CLASS}" type="page" row_id="{ID}" container_id="{~ID}" title="{TITLE}">
                    <td class="icon">
                        <div class="fa fa-file"></div>
                    </td>
                    <td class="name" colspan="5"><span class="value">{NAME}</span><span class="locked_icon fa fa-lock"></span></td>
                </tr>
                <!-- / -->
            </table>
            <table class="products">
                <!-- product -->
                <tr class="product row {DISABLED_CLASS} {NOT_PUBLISHED_CLASS} {LOCKED_CLASS}" n="{N}" type="product" product_id="{ID}" row_id="{ID}" container_id="{~ID}" title="{TITLE}">
                    <!-- if moderation_plugin -->
                    <td class="status icon {STATUS_CLASS}" title="{STATUS_TITLE}">
                        <div class="fa {ICON_CLASS}"></div>
                    </td>
                    <!-- / -->
                    <td class="has_image icon {HAS_IMAGES_CLASS}" title="{IMAGES_COUNT}">
                        <div class="fa fa-picture-o"></div>
                    </td>
                    <td class="name"><span class="value">{NAME}</span><span class="locked_icon fa fa-lock"></span></td>
                    <td class="stock" width="1">{STOCK}</td>
                    <td class="price" width="1">{PRICE}</td>
                </tr>
                <!-- / -->
            </table>
        </td>
    </tr>
</table>
