<?php
$string['pluginname']='Bugreport';
$string['pluginname_settings']='Bugreport - nastavení';
$string['bugreport_menuitem']='Bugreport';
$string['bugreport_title']='Bugreport - seznam hlášení';
$string['bugreport_title_nr']='Bugreport - detail chybového hlášení č.{$a->id}';
$string['bugreport_delete_confirm']='Opravdu chcete tento záznam smazat?';
$string['bugreport_delete_message']='Záznam byl úspěšně smazán';
$string['bugreport:read'] = 'Číst chyby';
$string['bugreport:write'] = 'Upravovat chyby';
$string['button_info_text_header']='Vlastní styl tlačítka';
$string['button_global_info_text_header']='Obecné nastavení tlačítka';
$string['send_mail_info_text_header']='Nastavení odeslání mailu';
$string['faq_global_info_text_header']='Nastavení FAQ';
$string['output_global_info_text_header']='Nastavení výpisu';

$string['show_unregistered_name']='Zobrazit nepřihlášeným uživatelům';
$string['show_unregistered_desc']='Zvolte, zda se má tlačítko zobrazit nepřihlášeným uživatelům';

$string['button_position_name']='Umístění tlačítka';
$string['button_position_desc']='Vyberte umístění tlačítka';
$string['button_scheme_name']='Barevné schéma tlačítka';
$string['button_scheme_desc']='Zvolte barevné schéma tlačítka. V případě, že vyberete hodnotu "Vlastní" aplikuje se níže uvedené nastavení (sekce "Vlastní styl tlačítka").<br /> Podle barevného stylu tlačítka se změní styl také u zobrazovaných dialogů.';
$string['button_title_name']='Popisek tlačítka';
$string['button_title_desc']='Zadejte popisek tlačítka';
$string['button_desc_name']='Titulek tlačítka';
$string['button_desc_desc']='Zadejte titulek tlačítka, který se zobrazí po najetí myši na tlačítko';
$string['button_icon_name']='Barva ikony';
$string['button_icon_desc']='Zvolte barvu ikony';
$string['button_bgcolor_name']='Barva pozadí tlačítka';
$string['button_bgcolor_desc']='Zvolte barvu pozadí tlačítka';
$string['button_fontcolor_name']='Barva textu tlačítka';
$string['button_fontcolor_desc']='Zvolte barvu textu tlačítka';

$string['button_style_default']='Vlastní';
$string['button_style_red']='Červená';
$string['button_style_green']='Zelená';
$string['button_style_blue']='Modrá';
$string['button_icon_style_white']='Bílá';
$string['button_icon_style_red']='Červená';
$string['button_icon_style_green']='Zelená';
$string['button_icon_style_blue']='Modrá';
$string['button_position_style_left']='Vlevo';
$string['button_position_style_right']='Vpravo';

$string['send_mail_subject']='Chybové hlášení č.{$a->id} z webu {$a->web}';
$string['send_mail_sendall_name']='E-mail zahrnuje všechny příjemce';
$string['send_mail_sendall_desc']='Pokud je toto pole zatržené, jako příjemce mailu budou uvedeny včechny osoby vyplněné v poli níže. V opačném případě bude uveden každý jeden příjemce.';
$string['send_mail_from_adress_name']='Odesílatel e-mailu';
$string['send_mail_from_adress_desc']='Prosím vyplňte odesílatele e-mailu';
$string['send_mail_recipients_name']='Seznam příjemců e-mailů';
$string['send_mail_recipients_desc']='Prosím vyplňte seznam příjemců e-mailů, kterým se odešle zpráva s chybovým hlášením. Jednotlivé maily oddělujte středníkem (;).<br />Pokud nebude vyplněna žádná hodnota, e-mail s chybovým hlášením <b>nebude</b> odeslán';
$string['send_mail_show_detaillink_name']='Zobrazit odkaz na detail';
$string['send_mail_show_detaillink_desc']='Zobrazit v e-mailu odkaz na detail chybového hlášení';

$string['faq_summary_name']='FAQ';
$string['faq_summary_desc']='';

$string['output_show_info_name']='Zobrazit sumarizaci chyby';
$string['output_show_info_desc']='Zvolte, zda zobrazit sumarizaci chyby během přesměrování po odeslání chybového hlášení';
$string['output_show_delay_name']='Prodleva mezi přesměrováním';
$string['output_show_delay_desc']='V případě, že máte povoleno zobrazení sumarizace chyb, zadejte prodlevu (ve vteřinách) mezi přesměrováním';
$string['output_log_count_name']='Počet zobrazených záznamů z logu';
$string['output_log_count_desc']='Zadejte počet zobrazovaných záznamů z tabulky log. Jedná se záznamy poslední činnosti uživatele a zobrazí se v detailu chybového hlášení.';
$string['output_show_info_opt_no']='Skrýt';
$string['output_show_info_opt_yes']='Zobrazit';
$string['output_alert']='Stránka byla odeslána';
$string['output_alert_notice']='Vaše chybové hlášení bylo odesláno';

$string['js_header_faq']='FAQ';
$string['js_header_type']='Typ chybového hlášení';
$string['js_header_simple']='Hlášení o chybě';
$string['js_header_screenshot']='Screenshot (hlášení chyby)';
$string['js_header_dialog']='Report bug';
$string['js_label_dialog_screenshot']='Screenshot chyby';
$string['js_label_dialog_description']='Detailní popis chyby';

$string['js_button_continue']='Hlásit chybu';
$string['js_button_showfaq']='Číst FAQ';
$string['js_button_send']='Odeslat';
$string['js_button_close']='Zavřít';
$string['js_button_type1']='Pouze text';
$string['js_button_type2']='Screenshot';

$string['js_faq_choice']='Než nahlásíte chybu, přečtěte si prosím často kladené dotazy.';
$string['js_type_desc']='Potřebujete chybu označit na&nbsp;screenshotu?';
$string['js_marker_head']='Popis chyby';

$string['js_string_marker_name']='Popis chyby';
$string['js_string_marker_hint']='Popište prosím chybu.';
$string['js_string_introtext']='<h2>Prosím vyznačte chybu</h2><p>Na níže zobrazeném screenshotu prosím vyznačte kliknutím hlášenou chybu. <br/>Přetažením okna označte umístění chyby a do šedého pole vyplňte její popis. Děkujeme.</p><p>(Pokud se screenshot nezobrazí, nahlašte prosím chybu pomocí <a id="bugrep-fallback" href="" title="Prosté hlášení">prostého hlášení</a>.)';
$string['js_string_browser_error']='--- Nepodporovaná verze prohlížeče ---';
$string['js_string_field_required']='Vyplnění tohoto pole je povinné';



$string['report_table_redirect_info']='Za {$a} vteřin budete automaticky přesměrováni. Pokud chcete pokračovat, klikněte na odkaz "Pokračovat" ve spodní části stránky';

$string['report_table_heading']='Podrobnosti o hlášené chybě';
$string['report_table_url']='URL adresa chyby:';
$string['report_table_browser']='Prohlížeč:';
$string['report_table_system']='Operační systém:';
$string['report_table_screen_res']='Rozlišení obrazovky:';
$string['report_table_browser_res']='Rozlišení prohlížeče:';
$string['report_table_username']='Uživatelské jméno:';
$string['report_table_error_detail']='Detailnější popis chyby:';
$string['report_table_preview']='Náhled obrazovky s chybou:';
$string['report_table_log']='Výpis z logu Moodle:';
$string['report_table_no_logged_user']='--- nepřihlášený ---';

$string['table_head_h1']='<h1>Detail chybového hlášení č.{$a}</h1>';
$string['table_head_h1_link']='Detail hlášení si můžete prohlédnout <a href="{$a}">zde</a>';
$string['table_head_detail']='Detail';
$string['table_head_action']='Akce';
$string['table_head_detail_link']='[Zpět na výpis chybových hlášení]';
$string['table_head_id']='ID';
$string['table_head_username']='Uživatelské jméno';
$string['table_head_current_url']='URL';
$string['table_head_report_image']='Náhled obrazovky s chybou';
$string['table_head_report_image_orig']='Náhled původní obrazovky';
$string['table_head_report_image_orig_a']='[PŮVODNÍ OBRAZOVKA]';
$string['table_head_error_desc']='Popis chyby';
$string['table_head_browser']='Prohlížeč';
$string['table_head_system']='Operační systém';
$string['table_head_log']='Log';
$string['table_head_screen_res']='Rozlišení obrazovky';
$string['table_head_browser_res']='Rozlišení prohlížeče';
$string['table_head_ts']='Čas nahlášení';

$string['table_detail_send_message']='[Odeslat zprávu]';