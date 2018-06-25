<?php

class MinC_View_Helper_Sidebar extends Zend_View_Helper_Abstract
{
    public function Sidebar($menu = [], $id = '', $urlMenuAjax = '')
    {
       ?>

        <aside id="sidebar-vue">
            <sidebar-menu
                <?= !empty($menu) ? ":menu='{$menu}'" : ''; ?>
                <?= !empty($id) ? ":id='{$id}'" : ''; ?>
                <?= !empty($urlMenuAjax) ? ":url-ajax='{$urlMenuAjax}'" : ''; ?>
            ></sidebar-menu>
        </aside>

        <script src="/public/js/vue.js" type="text/javascript"></script>
        <script src="/public/scripts/components/sidebar-menu.vue.js" type="text/javascript"></script>

        <script>
            new Vue({
                el: '#sidebar-vue'
            })
        </script>

        <?php
    }
}