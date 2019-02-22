<div class="{__NODE_ID__} focusable {FOCUS_CLASS}" instance="{__INSTANCE__}">

    <table class="folders estop">
        <!-- if not is_root -->
        <tr class="folder row parent" n="{N}" type="folder" row_id="{CAT_ID}" title="{PARENT_TITLE}">
            <td class="icon">
                <div class="fa fa-folder"></div>
            </td>
            <td class="name" colspan="4">..</td>
        </tr>
        <!-- / -->
        <!-- folder -->
        <tr class="folder row sortable {DISABLED_CLASS} {NOT_PUBLISHED_CLASS} {LOCKED_CLASS}" n="{N}" type="folder" row_id="{ID}" title="{TITLE}">
            <td class="icon">
                <div class="fa fa-folder"></div>
            </td>
            <td class="name" colspan="4">
                <div>
                    <span class="value">{NAME}</span><span class="locked_icon fa fa-lock"></span>
                    {*<div class="stat">
                        <span class="installed">{INSTALLED_PRODUCTS_COUNT}</span>/<span class="total">{PRODUCTS_COUNT}</span>
                    </div>*}
                </div>
            </td>
            <!-- / -->
        </tr>
    </table>
    <table class="products estop">
        <!-- product -->
        <tr class="product row {INSTALLED_CLASS} {DISABLED_CLASS} {NOT_PUBLISHED_CLASS} {LOCKED_CLASS}" n="{N}" type="product" row_id="{ID}" folder_id="{~CAT_ID}" title="{TITLE}">
            <td></td>
            <td class="icon has_image {HAS_IMAGES_CLASS}" title="{IMAGES_COUNT}">
                <div class="fa fa-picture-o"></div>
            </td>
            <td class="name"><span class="value">{NAME}</span><span class="locked_icon fa fa-lock"></span></td>
            <td class="stock" width="1">{STOCK}</td>
            <td class="price" width="1">{PRICE}</td>
            <td class="discount" width="1">{DISCOUNT}</td>
        </tr>
        <!-- / -->
    </table>

</div>
