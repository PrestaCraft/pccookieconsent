{literal}
    <script>
        window.addEventListener("load", function(){
            window.cookieconsent.initialise({
                "palette": {
                    "popup": {
                        "background": "{/literal}{$color_banner}{literal}",
                        "text": "{/literal}{$color_banner_text}{literal}"
                    },
                    "button": {
                        {/literal}{if $layout == 'wire'}{literal}
                        "background": "transparent",
                        "text": "{/literal}{$color_button_text}{literal}",
                        "border": "{/literal}{$color_button}{literal}"
                        {/literal}{else}{literal}
                        "background": "{/literal}{$color_button}{literal}",
                        "text": "{/literal}{$color_button_text}{literal}"
                        {/literal}{/if}{literal}
                    }
                },
                {/literal}{if $layout == 'classic'}{literal}
                "theme": "classic",
                {/literal}{/if}{literal}
                {/literal}{if $layout == 'edgeless'}{literal}
                "theme": "classic",
                {/literal}{/if}{literal}
                {/literal}{if $position == 'banner_top'}{literal}
                "position": "top",
                {/literal}{/if}{literal}
                {/literal}{if $position == 'banner_top_pushdown'}{literal}
                "position": "top",
                "static": true,
                {/literal}{/if}{literal}
                {/literal}{if $position == 'floating_left'}{literal}
                "position": "bottom-left",
                {/literal}{/if}{literal}
                {/literal}{if $position == 'floating_right'}{literal}
                "position": "bottom-right",
                {/literal}{/if}{literal}
                "content": {
                    "message": "{/literal}{$text_message}{literal}",
                    "dismiss": "{/literal}{$text_button}{literal}",
                    "link": "{/literal}{$text_link}{literal}"{/literal}{if $link_type != 'default' && $link_custom}{literal},
                    "href": "{/literal}{$link_custom}{literal}"
                    {/literal}{/if}{literal}
                }
            })});
    </script>
{/literal}