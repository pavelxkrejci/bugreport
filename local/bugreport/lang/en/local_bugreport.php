<?php
$string['pluginname']='Bugreport';
$string['pluginname_settings']='Bugreport - Settings';
$string['bugreport_menuitem']='Bugreport';
$string['bugreport_title']='Bugreport - List of reports';
$string['bugreport_title_nr']='Bugreport - Details of report No.{$a->id}';
$string['bugreport_delete_confirm']='Really delete this item?';
$string['bugreport_delete_message']='Item was deleted';
$string['bugreport:read'] = 'Read errors';
$string['bugreport:write'] = 'Edit errors';
$string['button_info_text_header']='Customize button';
$string['button_global_info_text_header']='General button settings';
$string['send_mail_info_text_header']='E-mail notifications settings';
$string['faq_global_info_text_header']='FAQ settings';
$string['output_global_info_text_header']='Output settings';

$string['show_unregistered_name']='Show to non-logged users';
$string['show_unregistered_desc']='Choose whether to show button to not-logged-in users';

$string['button_position_name']='Button position';
$string['button_position_desc']='Choose button position';
$string['button_scheme_name']='Button color scheme';
$string['button_scheme_desc']='Choose color scheme for report button. "Custom" will apply the below settings(section "Customize button").<br /> Dialog colors will change accordingly to button color scheme.';
$string['button_title_name']='Button name';
$string['button_title_desc']='Button name';
$string['button_desc_name']='Button title name';
$string['button_desc_desc']='Button title name generally shows on mouse over';
$string['button_icon_name']='Icon color';
$string['button_icon_desc']='Choose icon color';
$string['button_bgcolor_name']='Background button color';
$string['button_bgcolor_desc']='Choose background color';
$string['button_fontcolor_name']='Button text color';
$string['button_fontcolor_desc']='Choose text color';

$string['button_style_default']='Custom';
$string['button_style_red']='Red';
$string['button_style_green']='Green';
$string['button_style_blue']='Blue';
$string['button_icon_style_white']='White';
$string['button_icon_style_red']='Red';
$string['button_icon_style_green']='Green';
$string['button_icon_style_blue']='Blue';
$string['button_position_style_left']='Left';
$string['button_position_style_right']='Right';

$string['send_mail_subject']='Report No.{$a->id} from site: {$a->web}';
$string['send_mail_sendall_name']='Mail all recipients in single e-mail';
$string['send_mail_sendall_desc']='If checked all recipients addresses will be used in single e-mail report. Otherwise each recipient will receive his/her own e-mail.';
$string['send_mail_from_adress_name']='E-mail Sender address';
$string['send_mail_from_adress_desc']='Please enter sender e-mail address';
$string['send_mail_recipients_name']='List of recipients';
$string['send_mail_recipients_desc']='Please enter e-mails for all recipients. Separate e-mails with semicolon (;).<br />No e-mail can be sent without this value properly entered.';
$string['send_mail_show_detaillink_name']='Show link to report details';
$string['send_mail_show_detaillink_desc']='Show link to report details in e-mails';

$string['faq_summary_name']='FAQ';
$string['faq_summary_desc']='';

$string['output_show_info_name']='Show bug summary';
$string['output_show_info_desc']='Choose if you want to display bug summary during redirection after bug is reported.';
$string['output_show_delay_name']='Set delay for redirection';
$string['output_show_delay_desc']='In case of bug summary display during redirection, you can set delay (in seconds) for redirection';
$string['output_log_count_name']='Number of records displayed from Moodle log';
$string['output_log_count_desc']='Enter number of last x records from Moodle log. Those are the last user actions recorded before bug appeared.';
$string['output_show_info_opt_no']='Hide';
$string['output_show_info_opt_yes']='Show';
$string['output_alert']='Alert was sent';
$string['output_alert_notice']='Your bug report was sent successfuly.';

$string['js_header_faq']='FAQ';
$string['js_header_type']='Type of bug report';
$string['js_header_simple']='Bug report';
$string['js_header_screenshot']='Screenshot (bug report)';
$string['js_header_dialog']='Report bug';
$string['js_label_dialog_screenshot']='Screenshot of bug';
$string['js_label_dialog_description']='Description of bug';

$string['js_button_continue']='Report bug';
$string['js_button_showfaq']='Read FAQ';
$string['js_button_send']='Send';
$string['js_button_close']='Close';
$string['js_button_type1']='Text only';
$string['js_button_type2']='Screenshot';

$string['js_faq_choice']='Please read the FAQ before reporting bugs.';
$string['js_type_desc']='Do you need to mark the bug on&nbsp;screenshot?';
$string['js_marker_head']='Bug description';

$string['js_string_marker_name']='Bug description';
$string['js_string_marker_hint']='Please describe the problem.';
$string['js_string_introtext']='<h2>Please mark the problematic spot</h2><p>Please mark the reported bug on the screenshot below<br>by clicking. You can drag the marker / and you can fill description inside. Thank you!.</p><p>(If there is no screenshot , please report the bug using <a id="bugrep-fallback" href="" title="Prosté hlášení">Simple report</a>.)';
$string['js_string_browser_error']='--- Unsupported browser ---';
$string['js_string_field_required']='Required item';



$string['report_table_redirect_info']='You will be redirected in {$a} seconds. Click "Continue" below to continue now.';

$string['report_table_heading']='Reported bug details';
$string['report_table_url']='URL of the bug:';
$string['report_table_browser']='Browser:';
$string['report_table_system']='Operating system:';
$string['report_table_screen_res']='Display resolution:';
$string['report_table_browser_res']='Browser window:';
$string['report_table_username']='Username:';
$string['report_table_error_detail']='More bug details:';
$string['report_table_preview']='preview of screen with bug:';
$string['report_table_log']='Moodle log entries:';
$string['report_table_no_logged_user']='--- not logged in ---';

$string['table_head_h1']='<h1>Bug report details No.{$a}</h1>';
$string['table_head_h1_link']='You can view report details <a href="{$a}">here</a>';
$string['table_head_detail']='Detail';
$string['table_head_action']='Action';
$string['table_head_detail_link']='[Back to list]';
$string['table_head_id']='ID';
$string['table_head_username']='Username';
$string['table_head_current_url']='URL';
$string['table_head_report_image']='Screenshot preview';
$string['table_head_report_image_orig']='Original screenshot preview';
$string['table_head_report_image_orig_a']='[ORIGINAL SCREENSHOT]';
$string['table_head_error_desc']='Bug description';
$string['table_head_browser']='Browser';
$string['table_head_system']='Operating System';
$string['table_head_log']='Log';
$string['table_head_screen_res']='Display resolution';
$string['table_head_browser_res']='Browser window';
$string['table_head_ts']='Time of report';

$string['table_detail_send_message']='[Send message]';