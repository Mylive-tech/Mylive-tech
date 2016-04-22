
{literal}
    <script type="text/javascript">
            jQuery(document).ready(function(){
    {/literal}
    {foreach from=$validation_messages item=i key=k}
            {if $k eq 'maxlength' || $k eq 'minlength' || $k eq 'rangelength' || $k eq 'range' || $k eq 'max' || $k eq 'min'}
                                jQuery.validator.messages.{$k} = $.format("{$i}");
            {else}
                                jQuery.validator.messages.{$k} = "{$i}";
        {/if}
    {/foreach}
    {literal}

                    function fireEvent(obj, evt) {
                            var fireOnThis = obj;
                            if (document.createEvent) {
                                    var evObj = document.createEvent('MouseEvents');
                                    evObj.initEvent(evt, true, false);
                                    fireOnThis.dispatchEvent(evObj);
                            } else if (document.createEventObject) {
                                    fireOnThis.fireEvent('on'+evt);
                            }
                    }

                    //valid obj isntantiated in main.tpl
                    valid_obj.{/literal}{$form_id}{literal} = {
                                    debug: false,
                                    onKeyUp: true,
                                    onfocusout: false,
                                    errorElement: "span",
                                    errorClass: "errForm",
                                    submitHandler: function(form) {
                                            // do other stuff for a valid form
                                            if (jQuery("#{/literal}{$form_id}{literal}").valid()) {
                                                    form.submit();
                                            }
                                    },

    {/literal}{$validators}{literal}
                    };

                    jQuery("#{/literal}{$form_id}{literal}").validate(valid_obj.{/literal}{$form_id}{literal});

                    var selects = jQuery("#{/literal}{$form_id}{literal}").find("select");
                    var crt;

                    jQuery.each(selects, function() {
                        crt = this.id;
                        if(typeof(valid_obj.{/literal}{$form_id}{literal}.rules[crt]) !== 'undefined') {
                            jQuery("#"+crt).change(function() {
                              jQuery(this).valid();
                            });
                        }
                    });
            });
    </script>
{/literal}