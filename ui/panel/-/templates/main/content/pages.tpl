<div class="{__NODE_ID__} focusable {FOCUS_CLASS}" instance="{__INSTANCE__}">

    <table class="pages estop">
        <!-- if not is_root -->
        <tr class="page row parent" type="page" row_id="{CAT_ID}" title="CURRENT: {CURRENT_TITLE} PARENT: {PARENT_TITLE}">
            <td class="icon">
                <div class="fa fa-file"></div>
            </td>
            <td class="name" colspan="5">..</td>
        </tr>
        <!-- / -->
        <!-- page -->
        <tr class="page row sortable {DISABLED_CLASS} {NOT_PUBLISHED_CLASS} {LOCKED_CLASS}" type="page" row_id="{ID}" title="{TITLE}">
            <td class="icon">
                <div class="fa fa-file"></div>
            </td>
            <td class="name" colspan="5"><span class="value">{NAME}</span><span class="locked_icon fa fa-lock"></span></td>
        </tr>
        <!-- / -->
    </table>

    <div class="containers estop">
        <!-- container -->
        <div class="container sortable" container_id="{ID}">
            {CONTENT}
        </div>
        <!-- / -->
    </div>

</div>
