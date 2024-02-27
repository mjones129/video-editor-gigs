<div class="actions">
    <# if( is_admin || is_author){ #>
            <input type="hidden" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync'); ?>"/>
            <# if(  post_status == 'pending') { #>
                <ul>
                    <# if( !is_admin ){ #>
                        <li><a href="{{= edit_link }}" target="_blank" data-toggle="tooltip" data-placement="top"
                               title="<?php _e('Edit', 'enginethemes') ?>" class=""><i class="fa fa-pencil"></i></a>
                        </li>
                        <li><a href="#" data-action="delete" title="<?php _e('Delete', 'enginethemes') ?>"
                               data-toggle="tooltip" data-placement="top" class="action"><i
                                        class="fa fa-trash-o"></i></a></li>
                        <# } else { #>
                            <li><a href="#" data-action="approve" title="<?php _e('Approve', 'enginethemes') ?>"
                                   data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-check"></i></a>
                            </li>
                            <li><a href="#" data-action="reject" title="<?php _e('Reject', 'enginethemes') ?>"
                                   data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-times"></i></a>
                            </li>
                            <li><a href="{{= edit_link}}" target="_blank" data-action="edit"
                                   title="<?php _e('edit', 'enginethemes') ?>" data-toggle="tooltip"
                                   data-placement="top" class=""><i class="fa fa-pencil"></i></a></li>
                            <# } #>

                </ul>
                <# }else if( post_status == 'publish' && !is_search){ #>
                    <ul>
                        <# if( is_admin ){ #>
                            <li><a href="{{= edit_link}}" target="_blank" title="<?php _e('Edit', 'enginethemes') ?>"
                                   data-toggle="tooltip" data-placement="top" class=""><i class="fa fa-pencil"></i></a>
                            </li>
                            <li><a href="#" data-action="pause" title="<?php _e('Pause', 'enginethemes') ?>"
                                   data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-pause"></i></a>
                            </li>
                            <li><a href="#" data-action="archive" title="<?php _e('Archive', 'enginethemes') ?>"
                                   data-toggle="tooltip" data-placement="top" class="action"><i
                                            class="fa fa-archive"></i></a></li>
                            <# } else { #>
                                {{= edit_link_html }}
                                <li><a href="#" data-action="pause" title="<?php _e('Pause', 'enginethemes') ?>"
                                       data-toggle="tooltip" data-placement="top" class="action"><i
                                                class="fa fa-pause"></i></a></li>
                                <li><a href="#" data-action="archive" title="<?php _e('Archive', 'enginethemes') ?>"
                                       data-toggle="tooltip" data-placement="top" class="action"><i
                                                class="fa fa-archive"></i></a></li>
                                <# } #>
                    </ul>
                    <# }else if( post_status == 'archive' && !is_search){ #>
                        <ul>
                            <li><a href="<?php echo et_get_page_link('post-service'); ?>?id={{= ID }}"
                                   title="<?php _e('Renew', 'enginethemes') ?>" data-toggle="tooltip"
                                   data-placement="top"><i class="fa fa-refresh"></i></a></li>
                            <li><a href="#" data-action="delete" title="<?php _e('Delete', 'enginethemes') ?>"
                                   data-toggle="tooltip" data-placement="top" class="action"><i
                                            class="fa fa-trash-o"></i></a></li>
                        </ul>
                        <# }else if( post_status == 'reject' && !is_search){ #>
                            <ul>
                                <li><a href="{{= edit_link}}" target="_blank"
                                       title="<?php _e('Edit', 'enginethemes') ?>" data-toggle="tooltip"
                                       data-placement="top" class=""><i class="fa fa-pencil"></i></a></li>
                                <li><a href="#" data-action="delete" title="<?php _e('Delete', 'enginethemes') ?>"
                                       data-toggle="tooltip" data-placement="top" class="action"><i
                                                class="fa fa-trash-o"></i></a></li>
                            </ul>
                            <# }else if( post_status == 'pause' && !is_search){ #>
                                <ul>
                                    <# if( is_admin ){ #>
                                        <li><a href="#" data-action="unpause"
                                               title="<?php _e('Unpause', 'enginethemes') ?>" rel="tooltip"
                                               data-toggle="tooltip" data-placement="top" class="action"><i
                                                        class="fa fa-play"></i></a></li>
                                        <li><a href="{{= edit_link}}" target="_blank" data-action="edit"
                                               title="<?php _e('Edit', 'enginethemes') ?>" rel="tooltip"
                                               data-toggle="tooltip" data-placement="top" class=""><i
                                                        class="fa fa-pencil"></i></a></li>
                                        <li><a href="#" data-action="delete"
                                               title="<?php _e('Delete', 'enginethemes') ?>" rel="tooltip"
                                               data-toggle="tooltip" data-placement="top" class="action"><i
                                                        class="fa fa-trash-o"></i></a></li>
                                        <# } else { #>
                                            {{= edit_link_html }}
                                            <li><a href="#" data-action="unpause"
                                                   title="<?php _e('Unpause', 'enginethemes') ?>" rel="tooltip"
                                                   data-toggle="tooltip" data-placement="top" class="action"><i
                                                            class="fa fa-play"></i></a></li>
                                            <li><a href="#" data-action="delete"
                                                   title="<?php _e('Delete', 'enginethemes') ?>" rel="tooltip"
                                                   data-toggle="tooltip" data-placement="top" class="action"><i
                                                            class="fa fa-trash-o"></i></a></li>
                                            <# } #>
                                </ul>
                                <# }else if( post_status == 'draft' && !is_search){ #>
                                    <ul>
                                        <li><a href="<?php echo et_get_page_link('post-service'); ?>?id={{= ID }}"
                                               title="<?php _e('Submit', 'enginethemes') ?>" data-toggle="tooltip"
                                               data-placement="top"><i class="fa fa-arrow-up"></i></a></li>
                                        <li><a href="#" data-action="delete"
                                               title="<?php _e('Delete', 'enginethemes') ?>" data-toggle="tooltip"
                                               data-placement="top" class="action"><i class="fa fa-trash-o"></i></a>
                                        </li>
                                    </ul>
                                    <# }else if(!is_search){ #>
                                        <ul>
                                            <# if( is_admin ){ #>
                                                <li><a href="{{= edit_link}}" target="_blank"
                                                       title="<?php _e('Edit', 'enginethemes') ?>" data-toggle="tooltip"
                                                       data-placement="top" class=""><i class="fa fa-pencil"></i></a>
                                                </li>
                                                <li><a href="#" data-action="pause"
                                                       title="<?php _e('Pause', 'enginethemes') ?>"
                                                       data-toggle="tooltip" data-placement="top" class="action"><i
                                                                class="fa fa-pause"></i></a></li>
                                                <li><a href="#" data-action="archive"
                                                       title="<?php _e('Archive', 'enginethemes') ?>"
                                                       data-toggle="tooltip" data-placement="top" class="action"><i
                                                                class="fa fa-archive"></i></a></li>
                                                <# } else { #>
                                                    {{= edit_link_html }}
                                                    <li><a href="#" data-action="pause"
                                                           title="<?php _e('Pause', 'enginethemes') ?>"
                                                           data-toggle="tooltip" data-placement="top" class="action"><i
                                                                    class="fa fa-pause"></i></a></li>
                                                    <li><a href="#" data-action="archive"
                                                           title="<?php _e('Archive', 'enginethemes') ?>"
                                                           data-toggle="tooltip" data-placement="top" class="action"><i
                                                                    class="fa fa-archive"></i></a></li>
                                                    <# } #>
                                        </ul>
                                        <# } #>
        <# } #>
</div>