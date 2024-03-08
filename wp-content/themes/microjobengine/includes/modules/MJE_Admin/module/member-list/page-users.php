<div class="et-main-content user-container order-container">
    <div class="et-main-main">
        <div class="group-wrapper">
            <div class="group-fields">
                <div class="search-box et-member-search">
                    <p class="title-search"><?php _e("All Users", 'enginethemes'); ?></p>
                    <div id="search_users" class="function-filter">
                    <span class="filter-status">
                        <label class="check-filter">
                            <span><?php _e('Unconfirm', 'enginethemes'); ?></span>
                            <input type="checkbox" id="filter-status" value="1" name="user-status">
                        </label>
                    </span>
                        <span class="et-search-role">
                        <select name="role" id="" class="user-role et-input">
                            <option value=""><?php _e("All roles", 'enginethemes'); ?></option>
                            <?php foreach ($role_names as $role_name => $role_label) {
                                echo '<option value="' . $role_name . '" >' . $role_label . '</option>';
                            } ?>
                        </select>
                    </span>
                        <span class="et-search-input">
                        <input type="text" class="et-input user-search" name="keyword"
                               placeholder="<?php _e("Search users...", 'enginethemes'); ?>">
                           <i class="fa fa-search" aria-hidden="true"></i>
                    </span>
                    </div>
                </div>
                <!-- // user search box -->

                <div class="et-main-main no-margin clearfix overview list">
                    <div class="list-payment-package">
                        <ul class="row title-list">
                            <li class="col-md-4 col-sm-4"><?php _e("Member name", 'enginethemes'); ?></li>
                            <li class="col-md-2 col-sm-2"></li>
                            <li class="col-md-3 col-sm-3 time-request"><span class="sort-link"><a href="" class="sort-link" data-sort="sort_time" data-order="asc"><?php _e("Date Joined", 'enginethemes'); ?><i class="fa fa-sort" aria-hidden="true"></i></a></span></li>
                            <li class="col-md-3 col-sm-3 payment-type"><span class="sort-link"><a href="" class="sort-link" data-sort="sort_delivery" data-order="desc"><?php _e("Order delivery", 'enginethemes'); ?><i class="fa fa-sort" aria-hidden="true"></i></a></span></li>
                        </ul>
                        <ul class="list-inner list-payment users-list">
                            <?php
                            foreach ($users as $user) {
                                get_template_part('includes/modules/MJE_Admin/module/member-list/template/user', 'item');
                            } ?>
                        </ul>
                        <?php
                        echo '<script type="application/json" id="ae_users_list">';
                        echo json_encode(array('users' => $users, 'query' => $args));
                        echo '</script>';
                        ?>
                        <div class="paginations-wrapper">
                            <?php
                            if(isset($pagination))
                                echo $pagination;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- //user list -->
</div>
<?php
    require_once('template-js/user-item.php');