<?php
/**
 * Template for Email Footer
 */
$mail_footer = apply_filters('ae_get_mail_footer', '');
if ($mail_footer != '') return $mail_footer;

$info = apply_filters('ae_mail_footer_contact_info', get_option('blogname') . ' <br>
                        ' . get_option('admin_email') . ' <br>');

$customize = et_get_customization();
$copyright = apply_filters('mje_copyright_email', get_mje_copyright() );

$setting_link = et_get_page_link('settings');
$mail_footer = '</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="background: ' . $customize['background'] . '; padding: 10px 20px; color: #666;">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="vertical-align: top; text-align: left; width: 50%;">' . $copyright . '</td>
                                        <td style="text-align: right; width: 50%;">' . $info . '</td>
                                    </tr>
                                     <tr>

                                        <td style="text-align: right; width: 90%;" colspan="2"><small><a href="'.$setting_link.'">'.__('Update Notification Email','enginethemes').'</a></small></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        </table>
                    </div>
                    </body>
                    </html>';
echo $mail_footer;