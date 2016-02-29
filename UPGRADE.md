# Upgrade

## dev-master

### Filter

Update the schema `app/console doctrine:schema:update --force` and run following SQL-Statement:
 
```sql
UPDATE re_conditions SET value = CONCAT('"', value, '"') WHERE value NOT LIKE '"%"';
INSERT INTO `re_operators` (`id`, `operator`, `type`, `inputType`) VALUES
    (16, 'and', 5, 'tags'),
    (17, 'or', 5, 'tags'),
    (18, '=', 6, 'auto-complete'),
    (19, '!=', 6, 'auto-complete');
```

Additionally the filter by country has changed. Run following SQL script to update your filter conditions:

```sql
UPDATE `re_conditions` SET `field` = 'countryId', `type` = 6, `value` = CONCAT('"', (SELECT `id` FROM `co_countries` WHERE `code` = REPLACE(`re_conditions`. `value`, '"', '') LIMIT 1), '"') WHERE `field` = 'countryCode' AND `operator` != 'LIKE';

DELETE FROM `re_filters` WHERE `re_filters`.`id` IN (SELECT `re_condition_groups`.`idFilters` FROM `re_condition_groups` LEFT JOIN `re_conditions` ON `re_condition_groups`.`id` = `re_conditions`.`idConditionGroups` WHERE `re_conditions`.`operator` = 'LIKE');
```

Filter with a "like" condition for country (account and contact) will be lost after the upgrade because there is no
functionality for that anymore.

## 0.1.2

### Reindex-Command & Date Content-Type

First remove the version node `201511240844` with following command:

```bash
app/console doctrine:phpcr:node:remove /jcr:versions/201511240844
```

Then run the migrate command (`app/console phpcr:migrations:migrate`) to remove translated properties with non locale
and upgrade date-values within blocks.

## 1.1.0

### IndexName decorators from MassiveSearchBundle

The names of the indexes in the system can now be altered using decorators. There
is also a `PrefixDecorator`, which can prefixes the index name with an installation
specific prefix, which can be set using the `massive_search.metadata.prefix`
parameter.

The configuration parameter `massive_search.localization_strategy` have been removed.

The indexes have to be rebuilt using the following command:

```bash
app/console massive:search:index:rebuild --purge
```

### List-Toobar

To enable a sticky behaviour take a look at the documentation <http://docs.sulu.io/en/latest/bundles/admin/javascript-hooks/sticky-toolbar.html>

### Url Content-Type

The old upgrade for the url content-type don't upgrade properties in blocks. Rerun the migrate command to upgrade them.

```bash
app/console phpcr:migrations:migrate
```

### User locking

The locked toggler in the user tab of the contact section now sets the `locked`
field in the `se_users` table. Before this setting was written to the
`disabled` flag in the `co_contacts` table, which is removed now. If you have
used this field make sure to backup the data before applying the following
command:

```bash
app/console doctrine:schema:update --force
```

### Date Content-Type

The type of the date value in the database was wrong to update your existing data use following command:

```bash
app/console phpcr:migrations:migrate
```

### Media View Settings

The media collection thumbnailLarge view was removed from the media, 
to avoid an error, remove all `collectionEditListView` from the user settings table.

```sql
DELETE FROM `se_user_settings` WHERE `settingsKey` = 'collectionEditListView';
```
### Search

To index multiple fields (and `category_list` content-type) you have to add the attribute `type="array"` to the
`sulu.search.field` tag. The `tag_list` content-type has its own search-field type `tags`
(`<tag name="sulu.search.field" type="tags"/>`).

### Category Content-Type

The category content-type converts the selected ids into category data only for website rendering now. 

### System Collections

Remove the config `sulu_contact.form.avatar_collection` and note it you will need it in the sql statement below for the
placeholder `{old-avatar-collection}` (default value is `1`).

Update the database schema and then update the data-fixtures by running following sql statement.

```bash
app/console doctrine:schema:update --force
```

```sql
UPDATE me_collection_types SET collection_type_key='collection.default', name='Default' WHERE id=1;
INSERT INTO me_collection_types (id, name, collection_type_key) VALUES ('2', 'System Collections', 'collection.system');
```

The following sql statement moves the avatar images into the newly created system collections. To find the value for the
placeholder `{new-system-collection-id}` you can browse in the admin to the collection and note the `id` you find in the
url.

```sql
UPDATE me_media SET idCollections={new-system-collection-id} WHERE idCollections={old-avatar-collection};
```

### Search

The search mapping has to be changed, in particular the `index` tag. It is now
evaluated the same way as the other fields, so using `<index name="..."/>` will
now try to resolve the name of the index using a property from the given
object. If the old behavior is desired `<index value="..."/>` should be used
now.

Also the structure of the indexes has changed. Instead of one `page` index
containing all the pages this index is split into smaller ones after the scheme
`page_<webspace-key>`. This means that your own SearchController have to be
adapted. Additionally you have to rebuild your index, in order for these
changes to apply:

```bash
app/console massive:search:index:rebuild --purge
```

### Category
Category has now a default locale this has to set before use. You can use this sql statement after update your schema
(`app/console doctrine:schema:update --force`):

```sql
UPDATE ca_categories AS c SET default_locale = (SELECT locale FROM ca_category_translations WHERE idCategories = c.id LIMIT 1) WHERE default_locale = "";
```

### Websocket Component
The following Interfaces has new methods

Interface                                                             | Method                                                                  | Description
----------------------------------------------------------------------|-------------------------------------------------------------------------|---------------------------------------------------
Sulu/Component/Websocket/MessageDispatcher/MessageHandlerInterface    | onClose(ConnectionInterface $conn, MessageHandlerContext $context)      | will be called when a connection is closed or lost.
Sulu/Component/Websocket/MessageDispatcher/MessageDispatcherInterface | onClose(ConnectionInterface $conn, ConnectionContextInterface $context) | will be called when a connection is closed or lost.

### Logo/Avatar in Contact-Section
Can now be deleted from collection view. For that the database has to be updated.

```bash
app/console doctrine:schema:update --force
```

### Infinite scroll
The infinite-scroll-extension got refactored. To initialize infinite-scroll on an element, use
"this.sandbox.infiniteScroll.initialize(selector, callback)" instead of "this.sandbox.infiniteScroll(selector, callback)"
now. To unbind an infinite-scroll handler, use "this.sandbox.infiniteScroll.destroy(selector)"

### URL-ContentType
The URL-ContentType can now handle schemas like http or https. For that you have to add the default scheme to the
database records by executing following SQL statement:

```sql
UPDATE co_urls AS url SET url.url = CONCAT('http://', url.url) WHERE url.url NOT LIKE 'http://%';
```

To updated you content pages and snippets simply run:

```bash
app/console phpcr:migrations:migrate
```

Consider that the URL is now stored including the scheme (http://, ftp://, and so on), and therefore must not be
appended in the Twig template anymore.

### Media Metadata
Copyright field is now available in the metadata of medias. Therefore you have to update you database:

```bash
app/console doctrine:schema:update --force
```

### XML-Templates
Blocks now supports `minOccurs="0"` and `maxOccurs > 127`. For that the validation was improved and for both negative
values wont be supported anymore.

### Preview
The preview can now handle attributes and nested properties. To differentiate blocks and nested properties, it is now
necessary to add the property `typeof="collection"` to the root of a block `<div>` and 
`typeof="block" rel="name of block property"` to each child - see example.

__block:__

```twig
<div class="row" property="block" typeof="collection">
    {% for block in content.block %}
        <div rel="block" typeof="block">
            <h1 property="title">{{ block.title }}</h1>
        </div>
    {% endfor %}
</div>
```

__nested properties:__

```twig
<div property="is_winter">
    {% if content.is_winter %}
        <div property="article">{{ content.winter_article }}</div>
    {% endif %}
</div>
```

### ApiCategory
The function `getTranslation` was removed.  This avoid a INSERT SQL Exception when a serialization of categories
(without translation) is called in the same request.

### Registering JS-Routes
When registering backbone-routes now - instead of directly starting the corresponding component via 'this.html' - make your callback returning the component.
So for example the following:
``` js
sandbox.mvc.routes.push({
    route: 'contacts/accounts/edit::id/:content',
    callback: function(id) {
        this.html('<div data-aura-component="accounts/edit@sulucontact" data-aura-id="' + id + '"/>');
    }
});
```
becomes:
``` js
sandbox.mvc.routes.push({
    route: 'contacts/accounts/edit::id/:content',
    callback: function(id) {
        return '<div data-aura-component="accounts/edit@sulucontact" data-aura-id="' + id + '"/>';
    }
});
```

### Media Content Selection Type attribute changed

When you use the sulu media selection in your custom bundle you need to change the `data-type`.

**Before:**

``` html
<div id="{{ id|raw }}"
    ...
    data-type="mediaSelection"
    data-aura-component="media-selection@sulumedia"
    ...
</div>
```

**After:**
``` html
<div id="{{ id|raw }}"
    ...
    data-type="media-selection"
    data-aura-component="media-selection@sulumedia"
    ...
</div>
```

### Header
The header got a complete redesign, the breadcrumb and bottom-content are not available anymore. Also the event `header.set-toolbar` got marked as deprecated. The recommended way to start a sulu-header is via the header-hook of a view-component.

Some properties in the header-hook have changed, some are new, some not supported anymore. For a complete overview on the current properties in the header-hook see the documentation: http://docs.sulu.io/en/latest/bundles/admin/javascript-hooks/index.html

The major work when upgrading to the new header is to change the button-templates to sulu-buttons. Before you had to pass a template like e.g. 'default', which initialized a set of buttons, now each button is passed explicitly which gives you more flexibility. Lets have a look at an example:

**Before:**
```js
header: {
    tabs: {
        url: '/admin/content-navigations?alias=category'
    },
    toolbar: {
        template: 'default'
    }
}
```

**After:**
```js
header: {
    tabs: {
        url: '/admin/content-navigations?alias=category'
    },
    toolbar: {
        buttons: {
            save: {},
            settings: {
                options: {
                    dropdownItems: {
                        delete: {}
                    }
                }
            }
        }
    }
}
```

If you are using the `default` template in the header and now change to the sulu-buttons `save` and `delete` the emitted events changed.

| **Before**                    | **After**                    |
|-------------------------------|------------------------------|
| `sulu.header.toolbar.save`    | `sulu.toolbar.save`          |
| `sulu.header.toolbar.delete`  | `sulu.toolbar.delete`        |

Also the call for `disable`, `enable` and `loading` state of the `save` button has changed:

**Before:**

``` js
this.sandbox.emit('sulu.header.toolbar.state.change', 'edit', false); // enable
this.sandbox.emit('sulu.header.toolbar.state.change', 'edit', true, true); // disabled
this.sandbox.emit('sulu.header.toolbar.item.loading', 'save-button'); // loading 
```

**After:**

``` js
this.sandbox.emit('sulu.header.toolbar.item.enable', 'save', false); // enable
this.sandbox.emit('sulu.header.toolbar.item.disable', 'save', true); // disabled
this.sandbox.emit('sulu.header.toolbar.item.loading', 'save'); // loading 
```

#### Tabs
The tabs can be configured with the 'url', 'data' and 'container' option. The option 'fullControll' got removed. You can get the same effect by passing data with no 'component'-property.
For a complete overview on the current properties in the header-hook see the documentation: http://docs.sulu.io/en/latest/bundles/admin/javascript-hooks/index.html

#### Toolbar
The language-changer can be configured as it was. 'Template' and 'parentTemplate' in contrast are not supported anymore. Instead you pass an array of sulu-buttons.
Moreover the format of the buttons itself changed: https://github.com/massiveart/husky/blob/f9b3abeb547553c9c031710f1f98d0288b08ca9c/UPGRADE.md
Have a look at the documentation: http://docs.sulu.io/en/latest/bundles/admin/javascript-hooks/header.html

#### Language changer
The interface of the language-changer in the header hook stayed the same, however the emitted event changed from `sulu.header.toolbar.language-changed` to `sulu.header.language-changed`. A callback to this event recieves an object with an `id`- and a `title`-property.

**Before:**
```js
this.sandbox.on('sulu.header.toolbar.language-changed', this.languageChanged.bind(this));
// ...
languageChanged: function(locale) {
    this.options.locale = locale;
}
```

**After:**
```js
this.sandbox.on('sulu.header.language-changed', this.languageChanged.bind(this));
// ...
languageChanged: function(locale) {
    this.options.locale = locale.id;
}
```

#### Sulu-buttons
Buttons for toolbars get specified in an aura-extension (`sandbox.sulu.buttons` and `sandbox.sulu.buttons.dropdownItems`). Therfore each bundle can add their own buttons to the pool. The toolbar in the header fetches its buttons from this pool.
Have a look at the documentation: http://docs.sulu.io/en/latest/bundles/admin/sulu-buttons.html

#### List-toolbar
The 'inHeader' option got removed and is not supported anymore. `Sulu.buttons` are used internally and can be passed via the template which is recommended instead of using string templates.

#### Content-Types

Interface of Method has changed.

Old                                | New
-----------------------------------|---------------------------------------------------------------------
public function getDefaultParams() | public function getDefaultParams(PropertyInterface $property = null)

### Modified listbuilder to work with expressions

The listbuilder uses now expressions to build the query. In course of these changes some default values have been
removed from some methodes of the `AbstractListBuilder` because of unclear meaning / effect. Changed function parameters:

- where (conjunction removed)
- between (conjunction removed)

### Security

The security now requires its own phpcr namespace for storing security related information. To register this namespace
execute the following command.

```bash
app/console sulu:phpcr:init
```

### Enabled listbuilder to have multiple sort fields

It's now possible to have multiple sort fields by calling `sort()`. It's previous behavior was to always reset the sort
field, instead of adding a new one. Check if you haven't applied `sort()` multiple times to a listbuilder with the
purpose of overriding its previous sort field.

### Datagrid style upgrade

- Deleted options: fullwidth, stickyHeader, rowClickSelect
- The list-view with no margin was removed from the design. The view is still of width: 'max' but now with spacing on the left and right.
- For routing from a list to the edit the new option actionCallback should be used. For other actions like displaying additional information in the sidebar there exists a new option clickCallback. These two callbacks alow the component to adapt its style depending on if there is an action or not. For special cases there is still the item.click event.

### Filter conjunction field is nullable

```bash
app/console doctrine:schema:update --force
```

## 1.0.8

The `sulu_meta_seo` twig method does not render the canonical tag for shadow pages. Therefore this method is deprecated
and will be removed with Sulu 1.2. Use the new `sulu_seo` method instead. This method will also render the title, so
there is no need for the title block as it has been in the Sulu standard edition anymore.

## 1.0.6

### Configuration

The syntax of `sulu_core.locales` configuration has changed. It has to be defined with a translation. Additional the
translations of backend (currently only en/de) and a fallback locale can be configured.

```
sulu_core:
    locales:
        de: Deutsch
        en: English
    fallback_locale: 'en'
    translations: ['de', 'en']
```

## 1.0.4

### External link

If you have external-link pages created before 1.0.0 you should run the following command to fix them.
 
```
app/console phpcr:migrations:migrate
```

### Shadow-Pages

Filter values will now be copied from shadow-base locale to shadowed locale. Upgrade your data with following command:

```bash
app/console phpcr:migrations:migrate
```

### User serialization

The groups of the JMSSerializer for the users have changed. Make sure to include the group `fullUser` in the
`SerializationContext` if you are missing some fields in the serialized User.

## 1.0.0

### User / Role management changed

Service `sulu_security.role_repository` changed to `sulu.repository.role`.
Service `sulu_security.user_repository` should be avoided. Use `sulu.repository.user` instead.

### Snippets

Snippet state has been removed and set default to published. Therefor all snippets has to be set to published by this
running this command for each <locale>:

```bash
app/console doctrine:phpcr:nodes:update --query="SELECT * FROM [nt:unstructured] WHERE [jcr:mixinTypes] = 'sulu:snippet'" --apply-closure="\$node->setProperty('i18n:<locale>-state', 2);"
```

### Page-Templates

1. The tag `sulu.rlp` is now mandatory for page templates.
2. Page templates will now be filtered: only implemented templates in the theme will be displayed in the dropdown.
 
To find pages with not implemented templates run following command:

```bash
app/console sulu:content:validate <webspace-key>
```

To fix that pages, you could implement the template in the theme or save the pages with an other template over ui.

### Webspaces

1. The default-template config moved from global configuration to webspace config. For that it is needed to add this config to each webspace. 
2. The excluded xml tag has been removed from the webspace configuration file, so you have to remove this tag from all these files.

After that your webspace theme config should look like this:

```xml
<?xml version="1.0" encoding="utf-8"?>
<webspace xmlns="http://schemas.sulu.io/webspace/webspace"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:schemaLocation="http://schemas.sulu.io/webspace/webspace http://schemas.sulu.io/webspace/webspace-1.0.xsd">

...

    <theme>
        <key>default</key>
        <default-templates>
            <default-template type="page">default</default-template>
            <default-template type="homepage">overview</default-template>
        </default-templates>
    </theme>

...

</webspace>
```

Also remove the following default-template config for page and homepage from the file `app/config/config.yml`:

```yml
sulu_core:
    content:
        structure:
            default_type:
                snippet: "default"
-               page: "default"
-               homepage: "overview"
```

## 1.0.0-RC3

### Document Manager

The new Document Manager have been introduced, which means that a few files need to be updated.

There is a new namespace `Sulu\Component\Content\Compat`, which acts as a compatability layer. So all the controllers
extending the `DefaultController` and adapting its `indexAction` need to change a use statement from
`Sulu\Component\Content\StructureInterface` to `Sulu\Component\Content\Compat\StructureInterface`.

There are also some changes in the database, so you have to run the migrations:

```
app/console phpcr:migrations:migrate
```

In case the system still contains old data from the internal links content type you also have to run the upgrade command
again.

```
app/console sulu:upgrade:rc3:internal-links
```

The following classes have been moved, and every reference to them has to be updated:

Old name                                                 | New name
---------------------------------------------------------|---------------------------------------------------------------
Sulu\Component\Content\Event\ContentNodeDeleteEvent      | Sulu\Component\Content\Mapper\Event\ContentNodeDeleteEvent
Sulu\Component\Content\Event\ContentNodeEvent            | Sulu\Component\Content\Mapper\Event\ContentNodeEvent
Sulu\Component\Content\Event\ContentNodeOrderEvent       | Sulu\Component\Content\Mapper\Event\ContentNodeOrderEvent
Sulu\Component\Content\Block\BlockProperty               | Sulu\Component\Content\Compat\Block\BlockProperty
Sulu\Component\Content\Block\BlockPropertyInterface      | Sulu\Component\Content\Compat\Block\BlockPropertyInterface
Sulu\Component\Content\Block\BlockPropertyType           | Sulu\Component\Content\Compat\Block\BlockPropertyType
Sulu\Component\Content\Block\BlockPropertyWrapper        | Sulu\Component\Content\Compat\Block\BlockPropertyWrapper
Sulu\Component\Content\Section\SectionProperty           | Sulu\Component\Content\Compat\Section\SectionProperty
Sulu\Component\Content\Section\SectionPropertyInterface  | Sulu\Component\Content\Compat\Section\SectionPropertyInterface
Sulu\Component\Content\ErrorStructure                    | Sulu\Component\Content\Compat\ErrorStructure
Sulu\Component\Content\Section\MetaData                  | Sulu\Component\Content\Compat\MetaData
Sulu\Component\Content\Section\PageInterface             | Sulu\Component\Content\Compat\PageInterface
Sulu\Component\Content\Section\Property                  | Sulu\Component\Content\Compat\Property
Sulu\Component\Content\Section\PropertyInterface         | Sulu\Component\Content\Compat\PropertyInterface
Sulu\Component\Content\Section\PropertyParameter         | Sulu\Component\Content\Compat\PropertyParameter
Sulu\Component\Content\Section\PropertyTag               | Sulu\Component\Content\Compat\PropertyTag
Sulu\Component\Content\Section\Structure                 | Sulu\Component\Content\Compat\Structure
Sulu\Component\Content\Section\StructureInterface        | Sulu\Component\Content\Compat\StructureInterface
Sulu\Component\Content\Section\StructureManager          | Sulu\Component\Content\Compat\StructureManager
Sulu\Component\Content\Section\StructureManagerInterface | Sulu\Component\Content\Compat\StructureManagerInterface
Sulu\Component\Content\Section\StructureTag              | Sulu\Component\Content\Compat\StructureTag
Sulu\Component\Content\Section\StructureType             | Sulu\Component\Content\Compat\StructureType

### Upgrade commands

All the upgrade commands have been removed, since they are not of any use for
future versions of Sulu. The only exception is the
`sulu:upgrade:0.9.0:resource-locator` command, which has been renamed to
`sulu:content:resource-locator:maintain`.

### Contact management changed

Service `sulu_contact.contact_repository` changed to `sulu.repository.contact`.

| Removed methods                                                           | Use instead                                                               |
|---------------------------------------------------------------------------|---------------------------------------------------------------------------|
| `Sulu/Bundle/ContactBundle/Api/Account:addAccountAddresse`                | `Sulu/Bundle/ContactBundle/Api/Account:addAccountAddress`                 |
| `Sulu/Bundle/ContactBundle/Api/Account:removeAccountAddresse`             | `Sulu/Bundle/ContactBundle/Api/Account:removeAccountAddress`              |
| `Sulu/Bundle/ContactBundle/Api/Contact:addFaxe`                           | `Sulu/Bundle/ContactBundle/Api/Contact:addFax`                            |
| `Sulu/Bundle/ContactBundle/Api/Contact:removeFaxe`                        | `Sulu/Bundle/ContactBundle/Api/Contact:removeFax`                         |
| `Sulu/Bundle/ContactBundle/Api/Contact:addCategorie`                      | `Sulu/Bundle/ContactBundle/Api/Contact:addCategory`                       |
| `Sulu/Bundle/ContactBundle/Api/Contact:removeCategorie`                   | `Sulu/Bundle/ContactBundle/Api/Contact:removeCategory`                    |
| `Sulu/Bundle/ContactBundle/Entity/AbstractAccount:addAccountAddresse`     | `Sulu/Bundle/ContactBundle/Entity/AbstractAccount:addAccountAddress`      |
| `Sulu/Bundle/ContactBundle/Entity/AbstractAccount:removeAccountAddresse`  | `Sulu/Bundle/ContactBundle/Entity/AbstractAccount:removeAccountAddress`   |
| `Sulu/Bundle/ContactBundle/Entity/AbstractAccount:addCategorie`           | `Sulu/Bundle/ContactBundle/Entity/AbstractAccount:addCategory`            |
| `Sulu/Bundle/ContactBundle/Entity/AbstractAccount:removeCategorie`        | `Sulu/Bundle/ContactBundle/Entity/AbstractAccount:removeCategory`         |
| `Sulu/Bundle/ContactBundle/Entity/Address:addAccountAddresse`             | `Sulu/Bundle/ContactBundle/Entity/Address:addAccountAddress`              |
| `Sulu/Bundle/ContactBundle/Entity/Address:removeAccountAddresse`          | `Sulu/Bundle/ContactBundle/Entity/Address:removeAccountAddress`           |
| `Sulu/Bundle/ContactBundle/Entity/Address:addContactAddresse`             | `Sulu/Bundle/ContactBundle/Entity/Address:addContactAddress`              |
| `Sulu/Bundle/ContactBundle/Entity/Address:removeContactAddresse`          | `Sulu/Bundle/ContactBundle/Entity/Address:removeContactAddress`           |
| `Sulu/Bundle/ContactBundle/Entity/Contact:addFaxe`                        | `Sulu/Bundle/ContactBundle/Entity/Contact:addFax`                         |
| `Sulu/Bundle/ContactBundle/Entity/Contact:removeFaxe`                     | `Sulu/Bundle/ContactBundle/Entity/Contact:removeFax`                      |
| `Sulu/Bundle/ContactBundle/Entity/Contact:addContactAddresse`             | `Sulu/Bundle/ContactBundle/Entity/Contact:addContactAddress`              |
| `Sulu/Bundle/ContactBundle/Entity/Contact:removeContactAddresse`          | `Sulu/Bundle/ContactBundle/Entity/Contact:removeContactAddress`           |
| `Sulu/Bundle/ContactBundle/Entity/Contact:addCategorie`                   | `Sulu/Bundle/ContactBundle/Entity/Contact:addCategory`                    |
| `Sulu/Bundle/ContactBundle/Entity/Contact:removeCategorie`                | `Sulu/Bundle/ContactBundle/Entity/Contact:removeCategory`                 |

## 1.0.0-RC2

### Twig-Extensions

Following Twig-Functions has changed the name (new prefix for sulu functions):

| Before                    | Now                           |
|---------------------------|-------------------------------|
| `resolve_user`            | `sulu_resolve_user`           |
| `content_path`            | `sulu_content_path`           |
| `content_root_path`       | `sulu_content_root_path`      |
| `get_type`                | `sulu_get_type`               |
| `needs_add_button`        | `sulu_needs_add_button`       |
| `get_params`              | `sulu_get_params`             |
| `parameter_to_select`     | `sulu_parameter_to_select`    |
| `parameter_to_key_value`  | `sulu_parameter_to_key_value` |
| `content_load`            | `sulu_content_load`           |
| `content_load_parent`     | `sulu_content_load_parent`    |
| `get_media_url`           | `sulu_get_media_url`          |
| `meta_alternate`          | `sulu_meta_alternate`         |
| `meta_seo`                | `sulu_meta_seo`               |
| `navigation_root_flat`    | `sulu_navigation_root_flat`   |
| `navigation_root_tree`    | `sulu_navigation_root_tree`   |
| `navigation_flat`         | `sulu_navigation_flat`        |
| `navigation_tree`         | `sulu_navigation_tree`        |
| `breadcrumb`              | `sulu_breadcrumb`             |
| `sitemap_url`             | `sulu_sitemap_url`            |
| `sitemap`                 | `sulu_sitemap`                |
| `snippet_load`            | `sulu_snippet_load`           |

To automatically update this name you can run the following script. If your themes are not in the ClientWebsiteBundle
you have to change the folder in the second line.

```
#!/usr/bin/env bash
TWIGS=($(find ./src/Client/Bundle/WebsiteBundle/Resources/themes -type f -iname "*.twig"))

NAMES[0]="resolve_user"
NAMES[1]="content_path"
NAMES[2]="content_root_path"
NAMES[3]="get_type"
NAMES[4]="needs_add_button"
NAMES[5]="get_params"
NAMES[6]="parameter_to_select"
NAMES[7]="parameter_to_key_value"
NAMES[8]="content_load"
NAMES[9]="content_load_parent"
NAMES[10]="get_media_url"
NAMES[11]="meta_alternate"
NAMES[12]="meta_seo"
NAMES[13]="navigation_root_flat"
NAMES[14]="navigation_root_tree"
NAMES[15]="navigation_flat"
NAMES[16]="navigation_tree"
NAMES[17]="breadcrumb"
NAMES[18]="sitemap_url"
NAMES[19]="sitemap"
NAMES[20]="snippet_load"

for twig in ${TWIGS[*]}
do
    for name in ${NAMES[*]}
    do
        sed -i '' -e "s/$name/sulu_$name/g" $twig
    done
done
```

After running this script please check the changed files for conflicts and wrong replaces! 

### Website Navigation

Children of pages with the state "test" or pages which have the desired navigaiton context not assigned won't be moved
up in the hierarchy, instead they won't show up in the navigation at all.

## 1.0.0-RC1

### Security Roles
The identifiers in the acl_security_identities should be rename from SULU_ROLE_* to ROLE_SULU_*. This SQL snippet should 
do the job for you, you should adapt it to fit your needs:

UPDATE `acl_security_identities` SET `identifier` = REPLACE(`identifier`, 'SULU_ROLE_', 'ROLE_SULU_');

### Texteditor

The params for the texteditor content type where changed.

| Before                                        | Now                                                                                                           |
|-----------------------------------------------|---------------------------------------------------------------------------------------------------------------|
| `<param name="tables" value="true" />`        | `<param name="table" value="true" />`                                                                         |
| `<param name="links" value="true" />`         | `<param name="link" value="true" />`                                                                          |
| `<param name="pasteFromWord" value="true" />` | `<param name="paste_from_word" value="true" />`                                                               |
| `<param name="maxHeight" value="500" />`      | `<param name="max_height" value="500" />`                                                                     |
|                                               |                                                                                                               |
| `<param name="iframes" value="true" />`       | iframe and script tags can activated with an ckeditor parameter:                                              |
| `<param name="scripts" value="true" />`       | `<param name="extra_allowed_content" value="img(*)[*]; span(*)[*]; div(*)[*]; iframe(*)[*]; script(*)[*]" />` |


## 0.18.0

## Search index rebuild

Old data in search index can cause problems. You should clear the folder `app/data` and rebuild the index.
 
```bash
rm -rf app/data/*
app/console massive:search:index:rebuild
```

### Search adapter name changed

Adapter name changed e.g. from `massive_search_adapter.<adaptername>` to just `<adaptername>` in
configuration.

### Search index name changed

Pages and snippets are now indexed in separate indexes for pages and snippets.
Replace all instances of `->index('content')` with `->indexes(array('page',
'snippet')`.

### Search searches non-published pages by default

Pages which are "Test" are no longer indexed. If you require only
"published" pages modify your search query to start with: `state:published AND `
and escape the quotes:

```php
$hits = $searchManager
    ->createSearch(sprintf('state:published AND "%s"', str_replace('"', '\\"', $query)))
    ->locale($locale)
    ->index('page')
    ->execute();
```

### PHPCR: Doctrine-Dbal

The structure of data has changed. Run following command:

```bash
app/console doctrine:schema:update --force
```

### Smart content tag operator

The default operator for tags is now changed to OR. So you have to update with the following command,
because the previous default operator was AND.

```bash
app/console sulu:upgrade:0.18.0:smart-content-operator tag and
```

### Media Format Cache Public Folder

If you use the `sulu_media.format_cache.public_folder` parameter, 
the following configuration update need to be done,
because the parameter does not longer exists:

``` yml
sulu_media:
    format_cache:
        public_folder: 'public' # delete this line
        path: %kernel.root_dir%/../public/uploads/media # add this new configuration
```

### Admin

The `Sulu` prefix from all `ContentNavigationProviders` and `Admin` classes has
been removed. You have to change these names in all usages of this classes in
your own code.

### Media image converter commands

The image converter commands are now handled via service container tags. No need for the 
`sulu_media.image.command.prefix` anymore. If you have created your own command, you have to
tag your image converter command service with `sulu_media.image.command`.

Before:

```xml
<services>
    <service id="%sulu_media.image.command.prefix%blur" class="%acme.image.command.blur.class%" />
</services>
```

Change to:

```xml
<services>
    <service id="acme.image.command.blur" class="%acme.image.command.blur.class%">
        <tag name="sulu_media.image.command" alias="resize" />
    </service>
</services>
```

### Media preview urls

The thumbnail url will only be generated for supported mime-types. Otherwise it returns a zero length array.

To be sure that it is possible to generate a preview image you should check if the thumbnail url isset:

```twig
{% if media.thumbnails['200x200'] is defined %}
<img src="{{ media.thumbnails['200x200'] }}"/>
{% endif %}
```

### Error templates

Variables of exception template `ClientWebsiteBundle:error404.html.twig` has changed.

* `status_code`: response code 
* `status_text`: response text
* `exception`: whole exception object
* `currentContent`: content which was rendered before exception was thrown

Especially for 404 exception the `path` variable has been removed.

Before:
```twig
<p>The path "<em>{{ path }}</em>" does not exist.</p>
```

After:
```twig
<p>The path "<em>{{ request.resourceLocator }}</em>" does not exist.</p>
```

The behaviour of the errors has changed. In dev mode no custom error pages appears.
To see them you have to open following url:

```
{portal-prefix}/_error/{status_code}
sulu.lo/de/_error/500
```

More Information can be found in [sulu-docs](http://docs.sulu.io/en/latest/cookbook/custom-error-page.html).

To keep the backward compatibility you have to add following lines to your webspace configuration:

```xml
<webspace>
    ...
    
    <theme>
        ...
        
        <error-templates>
            <error-template code="404">ClientWebsiteBundle:views:error404.html.twig</error-template>
            <error-template default="true">ClientWebsiteBundle:views:error.html.twig</error-template>
        </error-templates>
    </theme>
    
    ...
</webspace>
```

### Twig Templates

If a page has no url for a specific locale, it returns now the resource-locator to the index page (`'/'`) instead of a
empty string (`''`).
 
__Before:__
```
urls = array(
    'de' => '/ueber-uns',
    'en' => '/about-us',
    'es' => ''
);
```

__After:__
```
urls = array(
    'de' => '/ueber-uns',
    'en' => '/about-us',
    'es' => '/'
);
```

### Util

The `Sulu\Component\Util\UuidUtils` has been removed. Use the `Phpcr\Utils\UuidHelper` instead.

## 0.17.0

### Media

Fill up the database column `me_collection_meta.locale` with the translated language like: `de` or `en`. If you
know you have only added collections in only one language you can use following sql statement:

```sql
UPDATE `me_collection_meta` SET `locale` = 'de';
```

Due to this it is possible that one collection has multiple metadata for one language. You have to remove this
duplicates by hand. For example one collection should have only one meta for the language `de`.

The collection and media has now a specific field to indicate which meta is default. For this run following commands.

```bash
app/console sulu:upgrade:0.17.0:collections
app/console sulu:upgrade:0.17.0:media
```

### Content navigation

The interfaces for the content navigation have been changed, so you have to
apply these changes if you have used a content navigation in your bundle.

Basically you can delete the `NavigationController` delivering the content
navigation items together with its routes. It's now common to suffix the
classes providing content navigation items with `ContentNavigationProvider`.

These classes have to implement the `ContentNavigationProviderInterface` and be
registered as services as described in the
[documentation](http://docs.sulu.io/en/latest/cookbook/using-the-tab-navigation.html).

Consider that the URLs for the retrieval of the content navigation items have
changed to `/admin/content-navigations?alias=your-alias` and have to be updated
in your javascript components.

### Contact and Account Security

The security checks are now also applied to contacts and accounts, make sure
that the users you want to have access have the correct permissions.

### Content

Behaviour of internal links has changed. It returns the link title for navigation/smartcontent/internal-link.

### Media Types

The media types are now set by wildcard check and need to be updated,
by running the following command: `sulu:media:type:update`.

### Media API Object

The `versions` attribute of the media API object changed from [array to object list](https://github.com/sulu-io/docs/pull/14/files).

### Contact

CRM-Components moved to a new bundle. If you enable the new Bundle everything should work as before.

BC-Breaks are:

 * AccountCategory replaced with standard Categories here is a migration needed
 
For a database upgrade you have to do following steps:

* The Account has no `type` anymore. This column has to be removed from `co_accounts` table.
* The table `co_account_categories` has to be removed manually.
* The table `co_terms_of_delivery` has to be removed manually.
* The table `co_terms_of_payment` has to be removed manually.
* `app/console doctrine:schema:update --force`

### Security

The names of some classes have changed like shown in the following table:

Old name                                                          | New name
------------------------------------------------------------------|--------------------------------------------------------------
Sulu\Bundle\SecurityBundle\Entity\RoleInterface                   | Sulu\Component\Security\Authentication\RoleInterface
Sulu\Component\Security\UserInterface                             | Sulu\Component\Security\Authentication\UserInterface
Sulu\Bundle\SecurityBundle\Factory\UserRepositoryFactoryInterface | Sulu\Component\Security\Authentication\UserRepositoryFactoryInterface
Sulu\Component\Security\UserRepositoryInterface                   | Sulu\Component\Security\Authentication\UserRepositoryInterface
Sulu\Bundle\SecurityBundle\Permission\SecurityCheckerInterface    | Sulu\Component\Security\Authorization\SecurityCheckerInterface

If you have used any of these interfaces you have to update them.

## 0.16.0

### Content Types

Time content types returns now standardized values (hh:mm:ss) and can handle this as localized string in the input
field.

For content you can upgrade the pages with:

```bash
app/console sulu:upgrade:0.16.0:time
```

In the website you should change the output if time to your format.

If you use the field in another component you should upgrade your api that it returns time values in format (hh:mm:ss).

### Security

Database has changed: User has now a unique email address. Run following command:

```bash
app/console doctrine:schema:update --force
```

## 0.15.0

### Sulu Locales
The Sulu Locales are not hardcoded anymore, but configured in the `app/config/config.yml` file:

```yml
sulu_core:
    locales: ["de","en"]
```

You have to add the locales to your configuration, otherwise Sulu will stop working.

### Internal Links

The internal representation of the internal links have changed, you have to run the following command to convert them:

```bash
app/console sulu:upgrade:0.15.0:internal-links
```

### Content Types

PropertyParameter are now able to hold metadata. Therefore the Interface has changed. Please check all your
ContentTypes which uses params.

__Before:__

DefaultParams:

```php
array(
    'name' => 'value',
    ...
)
```
Access in Twig:

```twig
{{ params.name }}
```

__After:__

```php
array(
    'name' => new PropertyParameter('name', 'string', 'value', array()),
    ...
)
```
Access in Twig:

```twig
As String:
{{ params.name }}

As Array or boolean:
{{ params.name.value }}

Get translated title:
{{ params.name.getTitle('de') }}
```

__Optional:__

Metadata under properties in template:

```xml
<property name="smart_content" type="smart_content">
    <params>
        <param name="display_as" type="collection">
            <param name="two">
                <meta>
                    <title lang="de">Zwei Spalten</title>
                    <title lang="en">Two columns</title>

                    <info_text lang="de">Die Seiten werden in zwei Spalten dargestellt</info_text>
                    <info_text lang="en">The pages would be displayed in two columns</info_text>
                </meta>
            </param>
        </param>
    </params>
</property>
```

### Websocket Component

Websocket start command changed to `app/console sulu:websocket:run`. If you use xdebug on your server please start
websockets with `app/console sulu:websocket:run -e prod`.

Default behavior is that websocket turned of for preview, if you want to use it turn it on in the
`app/config/admin/config.yml` under:

```yml
 sulu_content:
     preview:
         mode: auto       # possibilities [auto, on_request, off]
         websocket: false # use websockets for preview, if true it tries to connect to websocket server,
                          # if that fails it uses ajax as a fallback
         delay: 300       # used for the delayed send of changes, lesser delay are more request but less latency
```

### HTTP Cache

The HTTP cache integration has been refactored. The following configuration
must be **removed**:

````yaml
sulu_core:
    # ...
    http_cache:
        type: symfonyHttpCache
````

The Symfony HTTP cache is enabled by default now, so there is no need to do
anything else. See the [HTTP cache
documentation](http://sulu.readthedocs.org/en/latest/reference/bundles/http_cache.html)
for more information.

### Renamed RequestAnalyzerInterface methods

The text "Current" has been removed from all of the request analyzer methods.
If you used the request analyzer service then you will probably need to update
your code, see: https://github.com/sulu-cmf/sulu/pull/749/files#diff-23

## 0.14.0

* Role name is now unique
  * check roles and give them unique names
* Apply all permissions correctly, otherwise users won't be able to work on snippets, categories or tags anymore

## 0.13.0

* Remove `/cmf/<webspace>/temp` from repository
  * run `app/console doctrine:phpcr:node:remove /cmf/<webspace>/temp` foreach webspace

## 0.12.0

* Permissions have to be correct now, because they are applied
  * otherwise add a permission value of 120 for `sulu.security.roles`,
    `sulu.security.groups` and `sulu.security.users` to one user to change
    the settings in the UI
  * also check for the correct value in the `locale`-column of the `se_user_roles`-table
    * value has to be a json-string (e.g. `["en", "de"]`)
* Snippet content type defaults to all snippet types available instead of the
  default one
  * Explicitly define a snippet type in the parameters if this is not desired
