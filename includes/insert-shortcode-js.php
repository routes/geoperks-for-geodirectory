function sd_insert_shortcode() {
    $shortcode = document.getElementById("sd-shortcode-output").value;
    if ($shortcode) {
        console.log(jQuery("#wp-content-editor-container textarea").attr("aria-hidden"));
        if (tinyMCE && tinyMCE.activeEditor) {
            tinyMCE.execCommand('mceInsertContent', false, $shortcode);
        } else {
            var $txt = jQuery("#wp-content-editor-container textarea");
            var caretPos = $txt[0].selectionStart;
            var textAreaTxt = $txt.val();
            var txtToAdd = $shortcode;
            $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
        }
        tb_remove();
    }
}