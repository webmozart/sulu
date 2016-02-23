/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

define(['text!./form.html'], function(form) {

    'use strict';

    var defaults = {
            options: {
                data: {},
                instanceName: 'category',
                newCategoryTitle: 'sulu.category.new-category'
            },
            templates: {
                form: form,
                keyWordsUrl: '/admin/api/categories/<%= category %>/key-words<%= postfix %>?locale=<%= locale %><% if (typeof ids !== "undefined") { %>&ids=<%= ids.join(",") %><% } %>'
            },
            translations: {
                name: 'public.name',
                key: 'public.key',
                categoryKey: 'sulu.category.category-key',
                keyWords: 'sulu.category.key-words',
                keyWordDeleteLabel: 'labels.success.delete-desc',
                keyWordDeleteMessage: 'labels.success.delete-desc'
            }
        },

        constants = {
            detailsFromSelector: '#category-form',
            lastClickedCategorySettingsKey: 'categoriesLastClicked'
        };

    return {

        defaults: defaults,

        layout: {},

        /**
         * Initializes the collections list
         */
        initialize: function() {
            this.saved = true;
            this.locale = this.options.locale;

            this.prepareData(this.options.data);

            this.bindCustomEvents();
            this.render();

            if (!!this.data.id) {
                this.sandbox.sulu.saveUserSetting(constants.lastClickedCategorySettingsKey, this.data.id);
            }
        },

        /**
         * Prepare the data with fallbacks.
         */
        prepareData: function(data) {
            this.data = data;
            if (this.data.defaultLocale === this.data.locale && this.data.locale !== this.locale) {
                this.fallbackData = {locale: this.data.locale, name: this.data.name};
                this.data.name = null;
            }
            this.data.locale = this.locale;
        },

        /**
         * Binds custom related events
         */
        bindCustomEvents: function() {
            this.sandbox.on('sulu.header.back', function() {
                this.sandbox.emit('sulu.category.categories.list');
            }.bind(this));

            this.sandbox.on('sulu.header.language-changed', this.changeLanguage.bind(this));
            this.sandbox.on('sulu.toolbar.save', this.saveDetails.bind(this));
            this.sandbox.on('sulu.toolbar.delete', this.deleteCategory.bind(this));
            this.sandbox.on('sulu.category.categories.changed', this.changeHandler.bind(this));
        },

        /**
         * Triggered when locale was changed.
         *
         * @param {{id}} localeItem
         */
        changeLanguage: function(localeItem) {
            this.locale = localeItem.id;
        },

        /**
         * Renderes the details tab
         */
        render: function() {
            var placeholder = this.sandbox.translate('sulu.category.category-name');

            if (!!this.fallbackData) {
                placeholder = this.fallbackData.locale.toUpperCase() + ': ' + this.fallbackData.name;
            }

            this.sandbox.dom.html(
                this.$el,
                this.templates.form({
                    placeholder: placeholder,
                    translations: this.translations,
                    keyWords: !!this.options.data.id
                })
            );
            this.sandbox.form.create(constants.detailsFromSelector);
            this.sandbox.form.setData(constants.detailsFromSelector, this.data).then(function() {
                this.bindDomEvents();

                if (!!this.options.data.id) {
                    this.startKeyWordList();
                }
            }.bind(this));
        },

        /**
         * starts editable list for key-words.
         */
        startKeyWordList: function() {
            this.sandbox.sulu.initListToolbarAndList.call(
                this,
                'key-words',
                this.templates.keyWordsUrl({category: this.options.data.id, postfix: '/fields', locale: this.locale}),
                {
                    el: this.$find('#key-words-list-toolbar'),
                    template: this.sandbox.sulu.buttons.get({
                        add: {options: {position: 0}},
                        deleteSelected: {
                            options: {
                                position: 1, callback: function() {
                                    this.deleteKeyWords();
                                }.bind(this)
                            }
                        }
                    }),
                    parentTemplate: 'default',
                    listener: 'default'
                },
                {
                    el: this.$find('#key-words-list'),
                    url: this.templates.keyWordsUrl({category: this.options.data.id, postfix: '', locale: this.locale}),
                    resultKey: 'key-words',
                    searchFields: ['keyWord'],
                    saveParams: {locale: this.locale},
                    viewOptions: {
                        table: {
                            editable: true,
                            validation: true
                        }
                    }
                },
                'key-words'
            );

            // add clicked
            this.sandbox.on('sulu.toolbar.add', function() {
                this.sandbox.emit('husky.datagrid.record.add', {
                    id: '',
                    keyWord: '',
                    locale: this.locale
                });
            }.bind(this));
        },

        changeHandler: function(category) {
            this.prepareData(category);
            this.sandbox.form.setData(constants.detailsFromSelector, this.data);

            this.sandbox.emit('husky.datagrid.url.update', {locale: this.locale});
        },

        /**
         * Binds DOM-Events for the details tab
         */
        bindDomEvents: function() {
            // activate save-button on key input
            this.sandbox.dom.on(constants.detailsFromSelector, 'change keyup', function() {
                if (this.saved === true) {
                    this.sandbox.emit('sulu.header.toolbar.item.enable', 'save', false);
                    this.saved = false;
                }
            }.bind(this));
        },

        /**
         * Deletes the current category
         */
        deleteCategory: function() {
            if (!!this.data.id) {
                this.sandbox.emit('sulu.category.categories.delete', [this.data.id], null, function() {
                    this.sandbox.sulu.unlockDeleteSuccessLabel();
                    this.sandbox.emit('sulu.category.categories.list');
                }.bind(this));
            }
        },

        /**
         * Deletes the selected key-words.
         */
        deleteKeyWords: function() {
            this.sandbox.emit('husky.datagrid.items.get-selected', function(ids) {
                this.sandbox.sulu.showDeleteDialog(function(confirmed) {
                    if (confirmed === true) {
                        this.sandbox.util.save(
                            this.templates.keyWordsUrl({
                                category: this.options.data.id,
                                postfix: '',
                                locale: this.locale,
                                ids: ids
                            }),
                            'DELETE'
                        ).then(function() {
                            for (var i = 0, length = ids.length; i < length; i++) {
                                this.sandbox.emit('husky.datagrid.record.remove', ids[i]);
                            }

                            this.sandbox.emit(
                                'sulu.labels.success.show',
                                this.translations.keyWordDeleteMessage,
                                this.translations.keyWordDeleteLabel
                            );
                        }.bind(this));
                    }
                }.bind(this));
            }.bind(this));
        },

        /**
         * Saves the details-tab
         */
        saveDetails: function(action) {
            if (this.sandbox.form.validate(constants.detailsFromSelector)) {
                var data = this.sandbox.form.getData(constants.detailsFromSelector);
                this.data = this.sandbox.util.extend(true, {}, this.data, data);
                this.sandbox.emit('sulu.header.toolbar.item.loading', 'save');
                this.sandbox.emit('sulu.category.categories.save', this.data, this.savedCallback.bind(this, !this.data.id, action));
            }
        },

        /**
         * Method which gets called after the save-process has finished
         * @param {Boolean} toEdit if true the form will be navigated to the edit-modus
         * @param {String} action 'new', 'back' or 'edit
         * @param {Object} result the saved category model or the error model
         * @param {Boolean} success to trigger success callback, false to trigger error callback
         */
        savedCallback: function(toEdit, action, result, success) {
            if (success === true) {
                this.sandbox.emit('sulu.header.toolbar.item.disable', 'save', true);
                this.saved = true;
                if (action === 'back') {
                    this.sandbox.emit('sulu.category.categories.list');
                } else if (action === 'new') {
                    this.sandbox.emit('sulu.category.categories.form-add', this.options.parent);
                } else if (toEdit === true) {
                    this.sandbox.emit('sulu.category.categories.form', result.id);
                }
                this.sandbox.emit('sulu.labels.success.show', 'labels.success.category-save-desc', 'labels.success');
            } else {
                this.sandbox.emit('sulu.header.toolbar.item.enable', 'save', false);
                if (result.code === 1) {
                    this.sandbox.emit('sulu.labels.error.show', 'labels.error.category-unique-key', 'labels.error');
                } else {
                    this.sandbox.emit('sulu.labels.error.show', 'labels.success.category-save-error', 'labels.error');
                }
            }
        }
    };
});
