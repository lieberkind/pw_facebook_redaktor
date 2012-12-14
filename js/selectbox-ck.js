/*
 * jQuery selectbox plugin
 *
 * Copyright (c) 2007 Sadri Sahraoui (brainfault.com)
 * Licensed under the GPL license and MIT:
 *   http://www.opensource.org/licenses/GPL-license.php
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * The code is inspired from Autocomplete plugin (http://www.dyve.net/jquery/?autocomplete)
 *
 * Revision: $Id$
 * Version: 1.2
 * 
 * Changelog :
 *  Version 1.2 By Guillaume Vergnolle (web-opensource.com)
 *  - Add optgroup support
 *  - possibility to choose between span or input as replacement of the select box
 *  - support for jquery change event
 *  - add a max height option for drop down list
 *  Version 1.1 
 *  - Fix IE bug
 *  Version 1.0
 *  - Support jQuery noConflict option
 *  - Add callback for onChange event, thanks to Jason
 *  - Fix IE8 support
 *  - Fix auto width support
 *  - Fix focus on firefox dont show the carret
 *  Version 0.6
 *  - Fix IE scrolling problem
 *  Version 0.5 
 *  - separate css style for current selected element and hover element which solve the highlight issue 
 *  Version 0.4
 *  - Fix width when the select is in a hidden div   @Pawel Maziarz
 *  - Add a unique id for generated li to avoid conflict with other selects and empty values @Pawel Maziarz
 */jQuery.fn.extend({selectbox:function(e){return this.each(function(){new jQuery.SelectBox(this,e)})}});if(!window.console)var console={log:function(e){}};jQuery.SelectBox=function(e,t){function l(){o=0;a.hide()}function c(){a.append(b(f.attr("id"))).hide();var e=f.css("width");if(a.height()>n.maxHeight){a.width(parseInt(e)+parseInt(f.css("paddingRight"))+parseInt(f.css("paddingLeft")));a.height(n.maxHeight)}else a.width(e)}function h(e){var t=document.createElement("div");a=jQuery(t);a.attr("id",r+"_container");a.addClass(e.containerClass);a.css("display","none");return a}function p(e){if(n.inputType=="span"){var t=document.createElement("span"),i=jQuery(t);i.attr("id",r+"_input");i.addClass(e.inputClass);i.attr("tabIndex",u.attr("tabindex"))}else{var t=document.createElement("input"),i=jQuery(t);i.attr("id",r+"_input");i.attr("type","text");i.addClass(e.inputClass);i.attr("autocomplete","off");i.attr("readonly","readonly");i.attr("tabIndex",u.attr("tabindex"));i.css("width",u.css("width"))}return i}function d(e){var t=jQuery("li",a);if(!t||t.length==0)return!1;firstchoice=0;while(jQuery(t[firstchoice]).hasClass(n.groupClass))firstchoice++;i+=e;jQuery(t[i]).hasClass(n.groupClass)&&(i+=e);i<firstchoice?n.loopnoStep?i=t.size()-1:i=t.size():n.loopnoStep&&i>t.size()-1?i=firstchoice:i>t.size()&&(i=firstchoice);v(t,i);t.removeClass(n.hoverClass);jQuery(t[i]).addClass(n.hoverClass)}function v(e,t){var n=jQuery(e[t]).get(0),e=a.get(0);n.offsetTop+n.offsetHeight>e.scrollTop+e.clientHeight?e.scrollTop=n.offsetTop+n.offsetHeight-e.clientHeight:n.offsetTop<e.scrollTop&&(e.scrollTop=n.offsetTop)}function m(){var e=jQuery("li."+n.currentClass,a).get(0),t=(""+e.id).split("_"),r=t[t.length-1];if(n.onChangeCallback){u.get(0).selectedIndex=jQuery("li",a).index(e);n.onChangeParams={selectedVal:u.val()};n.onChangeCallback(n.onChangeParams)}else{u.val(r);u.change()}n.inputType=="span"?f.html(jQuery(e).html()):f.val(jQuery(e).html());return!0}function g(){return u.val()}function y(){return f.val()}function b(e){var t=new Array,r=document.createElement("ul");t=u.children("option");if(t.length==0){var i=new Array;i=u.children("optgroup");for(x=0;x<i.length;x++){t=jQuery("#"+i[x].id).children("option");var s=document.createElement("li");s.setAttribute("id",e+"_"+jQuery(this).val());s.innerHTML=jQuery("#"+i[x].id).attr("label");s.className=n.groupClass;r.appendChild(s);t.each(function(){var t=document.createElement("li");t.setAttribute("id",e+"_"+jQuery(this).val());t.innerHTML=jQuery(this).html();if(jQuery(this).is(":selected")){f.html(jQuery(this).html());jQuery(t).addClass(n.currentClass)}r.appendChild(t);jQuery(t).mouseover(function(e){o=1;n.debug&&console.log("over on : "+this.id);jQuery(e.target,a).addClass(n.hoverClass)}).mouseout(function(e){o=-1;n.debug&&console.log("out on : "+this.id);jQuery(e.target,a).removeClass(n.hoverClass)}).click(function(e){var t=jQuery("li."+n.hoverClass,a).get(0);n.debug&&console.log("click on :"+this.id);jQuery("li."+n.currentClass,a).removeClass(n.currentClass);jQuery(this).addClass(n.currentClass);m();u.get(0).blur();l()})})}}else t.each(function(){var t=document.createElement("li");t.setAttribute("id",e+"_"+jQuery(this).val());t.innerHTML=jQuery(this).html();if(jQuery(this).is(":selected")){f.val(jQuery(this).html());jQuery(t).addClass(n.currentClass)}r.appendChild(t);jQuery(t).mouseover(function(e){o=1;n.debug&&console.log("over on : "+this.id);jQuery(e.target,a).addClass(n.hoverClass)}).mouseout(function(e){o=-1;n.debug&&console.log("out on : "+this.id);jQuery(e.target,a).removeClass(n.hoverClass)}).click(function(e){var t=jQuery("li."+n.hoverClass,a).get(0);n.debug&&console.log("click on :"+this.id);jQuery("li."+n.currentClass,a).removeClass(n.currentClass);jQuery(this).addClass(n.currentClass);m();u.get(0).blur();l()})});return r}var n=t||{};n.inputType=n.inputType||"input";n.inputClass=n.inputClass||"selectbox";n.containerClass=n.containerClass||"selectbox-wrapper";n.hoverClass=n.hoverClass||"current";n.currentClass=n.currentClass||"selected";n.groupClass=n.groupClass||"groupname";n.maxHeight=n.maxHeight||200;n.loopnoStep=n.loopnoStep||!1;n.onChangeCallback=n.onChangeCallback||!1;n.onChangeParams=n.onChangeParams||!1;n.debug=n.debug||!1;var r=e.id,i=0,s=!1,o=0,u=jQuery(e),a=h(n),f=p(n);u.hide().before(f).before(a);c();f.click(function(){s||a.toggle()}).focus(function(){if(a.not(":visible")){s=!0;a.show()}}).keydown(function(e){switch(e.keyCode){case 38:e.preventDefault();d(-1);break;case 40:e.preventDefault();d(1);break;case 13:e.preventDefault();jQuery("li."+n.hoverClass).trigger("click");break;case 27:l()}}).blur(function(){a.is(":visible")&&o>0?n.debug&&console.log("container visible and has focus"):jQuery.browser.msie&&jQuery.browser.version.substr(0,1)<8||jQuery.browser.safari&&!/chrome/.test(navigator.userAgent.toLowerCase())?document.activeElement.getAttribute("id").indexOf("_container")==-1?l():f.focus():l()})};