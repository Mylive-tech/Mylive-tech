{literal}
<style type="text/css">
{/literal}{if $fontsEnabled == true}{literal}
{/literal}{$font_faces}{literal}

body, body * {
    font-family:{/literal} {$content_font}{literal};
}
h1,h2,h3,h4,h5,h6 {
    font-family:{/literal} {$header_font}{literal};
}

.headerLogo a {
    font-family:{/literal} {$site_name_font}{literal};
}

{/literal}{/if}{literal}
body {
{/literal}
{if !empty($background_pattern)}
    background-image: url(' {$background_pattern}');
{/if}
{if !empty($background_pattern)}
    background-color: {$background_color};
{/if}
{literal}
}
</style>
{/literal}