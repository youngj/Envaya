<?php
    $imgDir = "/_media/tiny_mce/themes/advanced/img";
    $iconsUrl = "$imgDir/icons.gif?v2";
    $skinDir = "/_media/tiny_mce/themes/advanced/skins/default/img";
?>

/* Reset */
.tSkin table, .tSkin tbody, .tSkin a, .tSkin img, .tSkin tr, .tSkin div, .tSkin td, .tSkin iframe, .tSkin span, .tSkin *, .tSkin .mceText {border:0; margin:0; padding:0; background:transparent; white-space:nowrap; text-decoration:none; font-weight:normal; cursor:default; color:#000; vertical-align:baseline; width:auto; border-collapse:separate; text-align:left}
.tSkin a:hover, .tSkin a:link, .tSkin a:visited, .tSkin a:active {text-decoration:none; font-weight:normal; cursor:default; color:#000}
.tSkin table td {vertical-align:middle}

/* Containers */
.tSkin table {direction:ltr; background:#F0F0EE}
.tSkin iframe {display:block; background:#FFF}
.tSkin .mceToolbar {height:26px}
.tSkin .mceLeft {text-align:left}
.tSkin .mceRight {text-align:right}

/* External */
.tSkin .mceExternalToolbar {position:absolute; border:1px solid #CCC; border-bottom:0; display:none;}
.tSkin .mceExternalToolbar td.mceToolbar {padding-right:13px;}
.tSkin .mceExternalClose {position:absolute; top:3px; right:3px; width:7px; height:7px; background:url(<?php echo $iconsUrl ?>) -820px 0}

/* Layout */
.tSkin table.mceLayout {border:0; border-left:1px solid #CCC; border-right:1px solid #CCC}
.tSkin table.mceLayout tr.mceFirst td {border-top:1px solid #CCC}
.tSkin table.mceLayout tr.mceLast td {border-bottom:1px solid #CCC}
.tSkin table.mceToolbar, .tSkin tr.mceFirst .mceToolbar tr td, .tSkin tr.mceLast .mceToolbar tr td {border:0; margin:0; padding:0;}
.tSkin td.mceToolbar {padding-top:1px; vertical-align:top}
.tSkin .mceIframeContainer {border-top:1px solid #CCC; border-bottom:1px solid #CCC}
.tSkin .mceStatusbar {font-family:'MS Sans Serif',sans-serif,Verdana,Arial; font-size:9pt; line-height:16px; overflow:visible; color:#000; display:block; height:20px}
.tSkin .mceStatusbar div {float:left; margin:2px}
.tSkin .mceStatusbar a.mceResize {display:block; float:right; background:url(<?php echo $iconsUrl ?>) -800px 0; width:20px; height:20px; cursor:se-resize; outline:0}
.tSkin .mceStatusbar a:hover {text-decoration:underline}
.tSkin table.mceToolbar {margin-left:3px}
.tSkin span.mceIcon, .tSkin img.mceIcon {display:block; width:20px; height:20px}
.tSkin .mceIcon {background:url(<?php echo $iconsUrl ?>) no-repeat 20px 20px}
.tSkin td.mceCenter {text-align:center;}
.tSkin td.mceCenter table {margin:0 auto; text-align:left;}
.tSkin td.mceRight table {margin:0 0 0 auto;}

/* Button */
.tSkin .mceButton {display:block; border:1px solid #F0F0EE; width:20px; height:20px; margin-right:1px}
.tSkin a.mceButtonEnabled:hover {border:1px solid #0A246A; background-color:#B2BBD0}
.tSkin a.mceButtonActive, .tSkin a.mceButtonSelected {border:1px solid #0A246A; background-color:#C2CBE0}
.tSkin .mceButtonDisabled .mceIcon {opacity:0.3; -ms-filter:'alpha(opacity=30)'; filter:alpha(opacity=30)}
.tSkin .mceButtonLabeled {width:auto}
.tSkin .mceButtonLabeled span.mceIcon {float:left}
.tSkin span.mceButtonLabel {display:block; font-size:10px; padding:4px 6px 0 22px; font-family:Tahoma,Verdana,Arial,Helvetica}
.tSkin .mceButtonDisabled .mceButtonLabel {color:#888}

/* Separator */
.tSkin .mceSeparator {display:block; background:url(<?php echo $iconsUrl ?>) -180px 0; width:2px; height:20px; margin:2px 2px 0 4px}

/* ListBox */
.tSkin .mceListBox, .tSkin .mceListBox a {display:block}
.tSkin .mceListBox .mceText {padding-left:4px; width:70px; text-align:left; border:1px solid #CCC; border-right:0; background:#FFF; font-family:Tahoma,Verdana,Arial,Helvetica; font-size:11px; height:20px; line-height:20px; overflow:hidden}
.tSkin .mceListBox .mceOpen {width:9px; height:20px; background:url(<?php echo $iconsUrl ?>) -741px 0; margin-right:2px; border:1px solid #CCC;}
.tSkin table.mceListBoxEnabled:hover .mceText, .tSkin .mceListBoxHover .mceText, .tSkin .mceListBoxSelected .mceText {border:1px solid #A2ABC0; border-right:0; background:#FFF}
.tSkin table.mceListBoxEnabled:hover .mceOpen, .tSkin .mceListBoxHover .mceOpen, .tSkin .mceListBoxSelected .mceOpen {background-color:#FFF; border:1px solid #A2ABC0}
.tSkin .mceListBoxDisabled a.mceText {color:gray; background-color:transparent;}
.tSkin .mceListBoxMenu {overflow:auto; overflow-x:hidden}
.tSkin .mceOldBoxModel .mceListBox .mceText {height:22px}
.tSkin .mceOldBoxModel .mceListBox .mceOpen {width:11px; height:22px;}
.tSkin select.mceNativeListBox {font-family:'MS Sans Serif',sans-serif,Verdana,Arial; font-size:7pt; background:#F0F0EE; border:1px solid gray; margin-right:2px;}

/* SplitButton */
/*
.tSkin .mceSplitButton {width:32px; height:20px; direction:ltr}
.tSkin .mceSplitButton a, .tSkin .mceSplitButton span {height:20px; display:block}
.tSkin .mceSplitButton a.mceAction {width:20px; border:1px solid #F0F0EE; border-right:0;}
.tSkin .mceSplitButton span.mceAction {width:20px; background-image:url(<?php echo $iconsUrl ?>);}
.tSkin .mceSplitButton a.mceOpen {width:9px; background:url(<?php echo $iconsUrl ?>) -741px 0; border:1px solid #F0F0EE;}
.tSkin .mceSplitButton span.mceOpen {display:none}
.tSkin table.mceSplitButtonEnabled:hover a.mceAction, .tSkin .mceSplitButtonHover a.mceAction, .tSkin .mceSplitButtonSelected a.mceAction {border:1px solid #0A246A; border-right:0; background-color:#B2BBD0}
.tSkin table.mceSplitButtonEnabled:hover a.mceOpen, .tSkin .mceSplitButtonHover a.mceOpen, .tSkin .mceSplitButtonSelected a.mceOpen {background-color:#B2BBD0; border:1px solid #0A246A;}
.tSkin .mceSplitButtonDisabled .mceAction, .tSkin .mceSplitButtonDisabled a.mceOpen {opacity:0.3; -ms-filter:'alpha(opacity=30)'; filter:alpha(opacity=30)}
.tSkin .mceSplitButtonActive a.mceAction {border:1px solid #0A246A; background-color:#C2CBE0}
.tSkin .mceSplitButtonActive a.mceOpen {border-left:0;}
*/

/* ColorSplitButton */
/*
.tSkin div.mceColorSplitMenu table {background:#FFF; border:1px solid gray}
.tSkin .mceColorSplitMenu td {padding:2px}
.tSkin .mceColorSplitMenu a {display:block; width:9px; height:9px; overflow:hidden; border:1px solid #808080}
.tSkin .mceColorSplitMenu td.mceMoreColors {padding:1px 3px 1px 1px}
.tSkin .mceColorSplitMenu a.mceMoreColors {width:100%; height:auto; text-align:center; font-family:Tahoma,Verdana,Arial,Helvetica; font-size:11px; line-height:20px; border:1px solid #FFF}
.tSkin .mceColorSplitMenu a.mceMoreColors:hover {border:1px solid #0A246A; background-color:#B6BDD2}
.tSkin a.mceMoreColors:hover {border:1px solid #0A246A}
.tSkin .mceColorPreview {margin-left:2px; width:16px; height:4px; overflow:hidden; background:#9a9b9a}
.tSkin .mce_forecolor span.mceAction, .tSkin .mce_backcolor span.mceAction {overflow:hidden; height:16px}
*/

/* Menu */
.tSkin .mceMenu {position:absolute; left:0; top:0; z-index:1000; border:1px solid #D4D0C8}
.tSkin .mceNoIcons span.mceIcon {width:0;}
.tSkin .mceNoIcons a .mceText {padding-left:10px}
.tSkin .mceMenu table {background:#FFF}
.tSkin .mceMenu a, .tSkin .mceMenu span, .tSkin .mceMenu {display:block}
.tSkin .mceMenu td {height:20px}
.tSkin .mceMenu a {position:relative;padding:3px 0 4px 0}
.tSkin .mceMenu .mceText {position:relative; display:block; font-family:Tahoma,Verdana,Arial,Helvetica; color:#000; cursor:default; margin:0; padding:0 25px 0 25px; display:block}
.tSkin .mceMenu span.mceText, .tSkin .mceMenu .mcePreview {font-size:11px}
.tSkin .mceMenu pre.mceText {font-family:Monospace}
.tSkin .mceMenu .mceIcon {position:absolute; top:0; left:0; width:22px;}
.tSkin .mceMenu .mceMenuItemEnabled a:hover, .tSkin .mceMenu .mceMenuItemActive {background-color:#dbecf3}
.tSkin td.mceMenuItemSeparator {background:#DDD; height:1px}
.tSkin .mceMenuItemTitle a {border:0; background:#EEE; border-bottom:1px solid #DDD}
.tSkin .mceMenuItemTitle span.mceText {color:#000; font-weight:bold; padding-left:4px}
.tSkin .mceMenuItemDisabled .mceText {color:#888}
.tSkin .mceMenuItemSelected .mceIcon {background:url(<?php echo $skinDir ?>/img/menu_check.gif)}
.tSkin .mceNoIcons .mceMenuItemSelected a {background:url(<?php echo $skinDir ?>/img/menu_arrow.gif) no-repeat -6px center}
.tSkin .mceMenu span.mceMenuLine {display:none}
.tSkin .mceMenuItemSub a {background:url(<?php echo $skinDir ?>/img/menu_arrow.gif) no-repeat top right;}

/* Progress,Resize */
.tSkin .mceBlocker {position:absolute; left:0; top:0; z-index:1000; opacity:0.5; -ms-filter:'alpha(opacity=50)'; filter:alpha(opacity=50); background:#FFF}
.tSkin .mceProgress {position:absolute; left:0; top:0; z-index:1001; background:url(<?php echo $skinDir ?>/img/progress.gif) no-repeat; width:32px; height:32px; margin:-16px 0 0 -16px}

/* Formats */
.tSkin .mce_formatPreview a {font-size:10px}
.tSkin .mce_p span.mceText {}
.tSkin .mce_address span.mceText {font-style:italic}
.tSkin .mce_pre span.mceText {font-family:monospace}
.tSkin .mce_h1 span.mceText {font-weight:bolder; font-size: 2em}
.tSkin .mce_h2 span.mceText {font-weight:bolder; font-size: 1.5em}
.tSkin .mce_h3 span.mceText {font-weight:bolder; font-size: 1.17em}
.tSkin .mce_h4 span.mceText {font-weight:bolder; font-size: 1em}
.tSkin .mce_h5 span.mceText {font-weight:bolder; font-size: .83em}
.tSkin .mce_h6 span.mceText {font-weight:bolder; font-size: .75em}

/* Theme */
.tSkin span.mce_bold {background-position:0 0}
.tSkin span.mce_italic {background-position:-60px 0}
.tSkin span.mce_underline {background-position:-140px 0}
.tSkin span.mce_strikethrough {background-position:-120px 0}
.tSkin span.mce_undo {background-position:-160px 0}
.tSkin span.mce_redo {background-position:-100px 0}
.tSkin span.mce_cleanup {background-position:-40px 0}
.tSkin span.mce_bullist {background-position:-20px 0}
.tSkin span.mce_numlist {background-position:-80px 0}
.tSkin span.mce_justifyleft {background-position:-460px 0}
.tSkin span.mce_justifyright {background-position:-480px 0}
.tSkin span.mce_justifycenter {background-position:-420px 0}
.tSkin span.mce_justifyfull {background-position:-440px 0}
.tSkin span.mce_anchor {background-position:-200px 0}
.tSkin span.mce_indent {background-position:-400px 0}
.tSkin span.mce_outdent {background-position:-540px 0}
.tSkin span.mce_link {background-position:-500px 0}
.tSkin span.mce_unlink {background-position:-640px 0}
.tSkin span.mce_sub {background-position:-600px 0}
.tSkin span.mce_sup {background-position:-620px 0}
.tSkin span.mce_removeformat {background-position:-580px 0}
.tSkin span.mce_newdocument {background-position:-520px 0}
.tSkin span.mce_image {background-position:-380px 0}
.tSkin span.mce_help {background-position:-340px 0}
.tSkin span.mce_code {background-position:-260px 0}
.tSkin span.mce_hr {background-position:-360px 0}
.tSkin span.mce_visualaid {background-position:-660px 0}
.tSkin span.mce_charmap {background-position:-240px 0}
.tSkin span.mce_paste {background-position:-560px 0}
.tSkin span.mce_copy {background-position:-700px 0}
.tSkin span.mce_cut {background-position:-680px 0}
.tSkin span.mce_blockquote {background-position:-220px 0}
.tSkin .mce_forecolor span.mceAction {background-position:-720px 0}
.tSkin .mce_backcolor span.mceAction {background-position:-760px 0}
.tSkin span.mce_forecolorpicker {background-position:-720px 0}
.tSkin span.mce_backcolorpicker {background-position:-760px 0}
.tSkin span.mce_document {background-position:-380px -20px}

/* Plugins */
/*
.tSkin span.mce_advhr {background-position:-0px -20px}
.tSkin span.mce_ltr {background-position:-20px -20px}
.tSkin span.mce_rtl {background-position:-40px -20px}
.tSkin span.mce_emotions {background-position:-60px -20px}
.tSkin span.mce_fullpage {background-position:-80px -20px}
.tSkin span.mce_fullscreen {background-position:-100px -20px}
.tSkin span.mce_iespell {background-position:-120px -20px}
.tSkin span.mce_insertdate {background-position:-140px -20px}
.tSkin span.mce_inserttime {background-position:-160px -20px}
.tSkin span.mce_absolute {background-position:-180px -20px}
.tSkin span.mce_backward {background-position:-200px -20px}
.tSkin span.mce_forward {background-position:-220px -20px}
.tSkin span.mce_insert_layer {background-position:-240px -20px}
.tSkin span.mce_insertlayer {background-position:-260px -20px}
.tSkin span.mce_movebackward {background-position:-280px -20px}
.tSkin span.mce_moveforward {background-position:-300px -20px}
.tSkin span.mce_media {background-position:-320px -20px}
.tSkin span.mce_nonbreaking {background-position:-340px -20px}
.tSkin span.mce_pastetext {background-position:-360px -20px}
.tSkin span.mce_selectall {background-position:-400px -20px}
.tSkin span.mce_preview {background-position:-420px -20px}
.tSkin span.mce_print {background-position:-440px -20px}
.tSkin span.mce_cancel {background-position:-460px -20px}
.tSkin span.mce_save {background-position:-480px -20px}
.tSkin span.mce_replace {background-position:-500px -20px}
.tSkin span.mce_search {background-position:-520px -20px}
.tSkin span.mce_styleprops {background-position:-560px -20px}
.tSkin span.mce_table {background-position:-580px -20px}
.tSkin span.mce_cell_props {background-position:-600px -20px}
.tSkin span.mce_delete_table {background-position:-620px -20px}
.tSkin span.mce_delete_col {background-position:-640px -20px}
.tSkin span.mce_delete_row {background-position:-660px -20px}
.tSkin span.mce_col_after {background-position:-680px -20px}
.tSkin span.mce_col_before {background-position:-700px -20px}
.tSkin span.mce_row_after {background-position:-720px -20px}
.tSkin span.mce_row_before {background-position:-740px -20px}
.tSkin span.mce_merge_cells {background-position:-760px -20px}
.tSkin span.mce_table_props {background-position:-980px -20px}
.tSkin span.mce_row_props {background-position:-780px -20px}
.tSkin span.mce_split_cells {background-position:-800px -20px}
.tSkin span.mce_template {background-position:-820px -20px}
.tSkin span.mce_visualchars {background-position:-840px -20px}
.tSkin span.mce_abbr {background-position:-860px -20px}
.tSkin span.mce_acronym {background-position:-880px -20px}
.tSkin span.mce_attribs {background-position:-900px -20px}
.tSkin span.mce_cite {background-position:-920px -20px}
.tSkin span.mce_del {background-position:-940px -20px}
.tSkin span.mce_ins {background-position:-960px -20px}
.tSkin span.mce_pagebreak {background-position:0 -40px}
.tSkin span.mce_restoredraft {background-position:-20px -40px}
.tSkin span.mce_spellchecker {background-position:-540px -20px}
*/