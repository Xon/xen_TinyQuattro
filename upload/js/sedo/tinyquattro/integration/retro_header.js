!function(n,t,i){n.fn.extend({_quattro_jqSerialize:n.fn.serialize,serialize:function(){if(t.tinyMCE)try{t.tinyMCE.triggerSave()}catch(n){}return this._quattro_jqSerialize()},_quattro_jqSerializeArray:n.fn.serializeArray,serializeArray:function(){if(t.tinyMCE)try{t.tinyMCE.triggerSave()}catch(n){}return this._quattro_jqSerializeArray()}}),XenForo.getEditorInForm=function(i,r,u,f){var e,o;if(f||(r=""),$form=n(i),$messageEditors=$form.find("textarea.MessageEditor"+(r||"")),$bbCodeEditors=$form.find(".bbCodeEditorContainer textarea"+(r||"")),$allEditors=$messageEditors.add($bbCodeEditors),$messageEditors.length==0)return!1;if($messageEditor=$messageEditors.eq(0),$bbCodesBbCodeEditor=$bbCodeEditors.eq(0),u&&u.length&&(e=!1,$allEditors.each(function(){if(n(this).attr("id")==u.attr("id")){e=n(this);return}}),e))return e.focus(),e;if(t.tinyMCE){var h=tinyMCE.activeEditor,c=n(h.getElement()),l=c.attr("id"),s=!1;if($messageEditors.each(function(){if(n(this).attr("id")==l){s=!0;return}}),h&&s&&!c.attr("disabled"))return tinyMCE.activeEditor;if(o=$messageEditor.attr("id"),o&&typeof tinyMCE.editors[o]!="undefined"&&!$messageEditor.attr("disabled"))return tinyMCE.editors[o]}return $messageEditor.attr("disabled")?$bbCodesBbCodeEditor.length?$bbCodesBbCodeEditor:!1:$messageEditor};var f=XenForo.speed.normal,u=XenForo.speed.fast;XenForo.AttachmentInserter=function(r){var u=!1;r.hover(function(){u=n(i.activeElement)}),r.click(function(i){function a(n,t){return n.hasClass("NoAttachment")&&n.hasClass("ImgFallback")?h:t}var c=r.closest(".AttachedFile").find(".Thumbnail a"),s=c.data("attachmentid"),f,e,h,o,v=XenForo._baseUrl,y=c.find("img").attr("src"),l=c.attr("href");i.preventDefault(),r.attr("name")=="thumb"?(e="[ATTACH]"+s+"[/ATTACH] ",h="[img]"+v+y+"[/img]",o='<img src="'+y+'" class="attachThumb bbCodeImage" alt="attachThumb'+s+'" /> '):(e="[ATTACH=full]"+s+"[/ATTACH] ",h="[img]"+v+l+"[/img]",o='<img src="'+l+'" class="attachFull bbCodeImage" alt="attachFull'+s+'" /> '),f=XenForo.getEditorInForm(r.closest("form"),"",u),f.execCommand&&t.tinyMCE?($textarea=n(f.getElement()),o=a($textarea,o),f.execCommand("mceInsertContent",!1,o)):(e=a(f,e),f.val(f.val()+e))})},XenForo.AttachmentDeleter=function(t){t.css("display","block").click(function(t){var i=n(t.target),o=i.attr("href")||i.data("href"),r=i.closest(".AttachedFile"),s=i.closest(".AttachedFile").find(".Thumbnail a"),f=s.data("attachmentid"),e;if(o)return r.xfFadeUp(XenForo.speed.normal,null,u,"swing"),XenForo.ajax(o,"",function(n){if(XenForo.hasResponseError(n))return r.xfFadeDown(XenForo.speed.normal),!1;var i=r.closest(".AttachmentEditor");r.xfRemove(null,function(){i.trigger("AttachmentsChanged")},u,"swing")}),f&&(e=XenForo.getEditorInForm(i.closest("form"),":not(.NoAttachment)",!1,!0),typeof e.getBody()!="undefined"&&n(e.getBody()).find("img[alt=attachFull"+f+"], img[alt=attachThumb"+f+"]").remove()),!1;console.warn("Unable to locate href for attachment deletion from %o",i)})}}(jQuery,this,document);