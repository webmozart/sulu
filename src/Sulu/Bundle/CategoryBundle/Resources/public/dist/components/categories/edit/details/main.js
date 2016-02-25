define(["text!./form.html"],function(a){"use strict";var b={options:{data:{},instanceName:"category",newCategoryTitle:"sulu.category.new-category"},templates:{form:a,keyWordsUrl:'/admin/api/categories/<%= category %>/key-words<% if (typeof id !== "undefined") { %>/<%= id %><% } %><% if (typeof postfix !== "undefined") { %><%= postfix %><% } %>?locale=<%= locale %><% if (typeof ids !== "undefined") { %>&ids=<%= ids.join(",") %><% } %><% if (typeof force !== "undefined") { %>&force=<%= force %><% } %>'},translations:{name:"public.name",key:"public.key",yes:"public.yes",no:"public.no",categoryKey:"sulu.category.category-key",keyWords:"sulu.category.key-words",keyWordDeleteLabel:"labels.success.delete-desc",keyWordDeleteMessage:"labels.success.delete-desc",conflictTitle:"sulu.category.keyword_conflict.title",conflictMessage:"sulu.category.keyword_conflict.message",conflictOverwrite:"sulu.category.keyword_conflict.overwrite",conflictDetach:"sulu.category.keyword_conflict.detach",mergeTitle:"sulu.category.keyword_merge.title",mergeMessage:"sulu.category.keyword_merge.message"}},c={detailsFromSelector:"#category-form",lastClickedCategorySettingsKey:"categoriesLastClicked"};return{defaults:b,layout:{},initialize:function(){this.saved=!0,this.locale=this.options.locale,this.prepareData(this.options.data),this.bindCustomEvents(),this.render(),this.data.id&&this.sandbox.sulu.saveUserSetting(c.lastClickedCategorySettingsKey,this.data.id)},prepareData:function(a){this.data=a,this.data.defaultLocale===this.data.locale&&this.data.locale!==this.locale&&(this.fallbackData={locale:this.data.locale,name:this.data.name},this.data.name=null),this.data.locale=this.locale},bindCustomEvents:function(){this.sandbox.on("sulu.header.back",function(){this.sandbox.emit("sulu.category.categories.list")}.bind(this)),this.sandbox.on("sulu.header.language-changed",this.changeLanguage.bind(this)),this.sandbox.on("sulu.toolbar.save",this.saveDetails.bind(this)),this.sandbox.on("sulu.toolbar.delete",this.deleteCategory.bind(this)),this.sandbox.on("sulu.category.categories.changed",this.changeHandler.bind(this))},changeLanguage:function(a){this.locale=a.id},render:function(){var a=this.sandbox.translate("sulu.category.category-name");this.fallbackData&&(a=this.fallbackData.locale.toUpperCase()+": "+this.fallbackData.name),this.sandbox.dom.html(this.$el,this.templates.form({placeholder:a,translations:this.translations,keyWords:!!this.options.data.id})),this.sandbox.form.create(c.detailsFromSelector),this.sandbox.form.setData(c.detailsFromSelector,this.data).then(function(){this.bindDomEvents(),this.options.data.id&&this.startKeyWordList()}.bind(this))},startKeyWordList:function(){this.sandbox.sulu.initListToolbarAndList.call(this,"key-words",this.templates.keyWordsUrl({category:this.options.data.id,postfix:"/fields",locale:this.locale}),{el:this.$find("#key-words-list-toolbar"),template:this.sandbox.sulu.buttons.get({add:{options:{position:0}},deleteSelected:{options:{position:1,callback:function(){this.deleteKeyWords()}.bind(this)}}}),parentTemplate:"default",listener:"default"},{el:this.$find("#key-words-list"),url:this.templates.keyWordsUrl({category:this.options.data.id,locale:this.locale}),resultKey:"key-words",searchFields:["keyWord"],saveParams:{locale:this.locale},viewOptions:{table:{editable:!0,validation:!0}}},"key-words"),this.sandbox.on("sulu.toolbar.add",function(){this.sandbox.emit("husky.datagrid.record.add",{id:"",keyWord:"",locale:this.locale})}.bind(this)),this.sandbox.on("husky.datagrid.data.save.failed",function(a,b,c,d){this.handleFail(a,d)}.bind(this))},handleFail:function(a,b){409===a.status&&2002===a.responseJSON.code?this.handleConflict(b.id,b.keyWord):409===a.status&&2001===a.responseJSON.code&&this.resolveConflict("merge",b.id,b.keyWord)},handleConflict:function(a,b){var c=this.sandbox.dom.createElement("<div/>");this.$el.append(c),this.sandbox.start([{name:"overlay@husky",options:{el:c,cssClass:"alert",removeOnClose:!0,openOnStart:!0,instanceName:"warning",slides:[{title:this.translations.conflictTitle,message:this.translations.conflictMessage,okCallback:function(){this.resolveConflict("overwrite",a,b)}.bind(this),cancelCallback:function(){},buttons:[{text:this.translations.conflictOverwrite,type:"ok",align:"right"},{text:this.translations.conflictDetach,align:"center",callback:function(){this.resolveConflict("detach",a,b),this.sandbox.emit("husky.overlay.warning.close")}.bind(this)},{type:"cancel",align:"left"}]}]}}])},resolveConflict:function(a,b,c){var d={id:b,keyWord:c};this.sandbox.util.save(this.templates.keyWordsUrl({category:this.options.data.id,id:b,locale:this.locale,force:a}),"PUT",d).then(function(a){a.id!==d.id?(this.sandbox.emit("husky.datagrid.record.remove",d.id),this.sandbox.emit("husky.datagrid.record.add",a)):this.sandbox.emit("husky.datagrid.records.change",d)}.bind(this)).fail(function(a){this.handleFail(a,d)}.bind(this))},changeHandler:function(a){this.prepareData(a),this.sandbox.form.setData(c.detailsFromSelector,this.data),this.sandbox.emit("husky.datagrid.url.update",{locale:this.locale})},bindDomEvents:function(){this.sandbox.dom.on(c.detailsFromSelector,"change keyup",function(){this.saved===!0&&(this.sandbox.emit("sulu.header.toolbar.item.enable","save",!1),this.saved=!1)}.bind(this),"input:not(.editable-input)")},deleteCategory:function(){this.data.id&&this.sandbox.emit("sulu.category.categories.delete",[this.data.id],null,function(){this.sandbox.sulu.unlockDeleteSuccessLabel(),this.sandbox.emit("sulu.category.categories.list")}.bind(this))},deleteKeyWords:function(){this.sandbox.emit("husky.datagrid.items.get-selected",function(a){this.sandbox.sulu.showDeleteDialog(function(b){b===!0&&this.sandbox.util.save(this.templates.keyWordsUrl({category:this.options.data.id,locale:this.locale,ids:a}),"DELETE").then(function(){for(var b=0,c=a.length;c>b;b++)this.sandbox.emit("husky.datagrid.record.remove",a[b]);this.sandbox.emit("sulu.labels.success.show",this.translations.keyWordDeleteMessage,this.translations.keyWordDeleteLabel)}.bind(this))}.bind(this))}.bind(this))},saveDetails:function(a){if(this.sandbox.form.validate(c.detailsFromSelector)){var b=this.sandbox.form.getData(c.detailsFromSelector);this.data=this.sandbox.util.extend(!0,{},this.data,b),this.sandbox.emit("sulu.header.toolbar.item.loading","save"),this.sandbox.emit("sulu.category.categories.save",this.data,this.savedCallback.bind(this,!this.data.id,a))}},savedCallback:function(a,b,c,d){d===!0?(this.sandbox.emit("sulu.header.toolbar.item.disable","save",!0),this.saved=!0,"back"===b?this.sandbox.emit("sulu.category.categories.list"):"new"===b?this.sandbox.emit("sulu.category.categories.form-add",this.options.parent):a===!0&&this.sandbox.emit("sulu.category.categories.form",c.id),this.sandbox.emit("sulu.labels.success.show","labels.success.category-save-desc","labels.success")):(this.sandbox.emit("sulu.header.toolbar.item.enable","save",!1),1===c.code?this.sandbox.emit("sulu.labels.error.show","labels.error.category-unique-key","labels.error"):this.sandbox.emit("sulu.labels.error.show","labels.success.category-save-error","labels.error"))}}});