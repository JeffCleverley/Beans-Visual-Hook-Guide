=== Beans Visual Hook Guide ===
Contributors: Deftly, hellofromtonya
Tags: Beans, Beans Framework, Beans HTML API, Development Tool, Hooks
Donate link: https://www.paypal.me/jeffcleverley
Requires at least: 4.6
Tested up to: 4.9.4
Requires PHP: 5.6
Stable tag: trunk
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A useful companion tool for theme development with the Beans Framework.

Displays all possible Markup Action Hooks made available by the Beans HTML API.

== Description ==
A Plugin tool to aid theme development with the innovative, flexible, and incredibly powerful [Beans](https://www.getbeans.io/) Framework.

When enabled alongside Beans Development Mode, this plugin displays all possible Markup Action Hooks made available by the Beans HTML API.

Beans is a dream to develop with, as all markup and attributes added using Beans are registered using a unique Markup ID which can be exposed by enabling the Beans Development Mode in settings.

Once Development Mode is enabled, the Markup IDs are output in a data-markup-id tag in the front-end. The values of which can be used by the various [Beans APIs](https://www.getbeans.io/code-reference/api/) to rapidly develop beautiful themes.

Any markup added using Beans adds several [dynamic action hooks](https://www.getbeans.io/documentation/markup-and-attributes/) both before and after it:

`
{$markup_id}_before_markup, fires before the opening markup
{$markup_id}_prepend_markup, fires after the opening markup
{$markup_id}_append_markup, fires before the closing markup
{$markup_id}_after_markup, fires after the closing markup.
`

( _prepend_markup and _append_markup are not available for self-closed markup )

In short, this means pretty much anything can be added anywhere on any page by adding actions to the available hooks.

This plugin is intended as a companion tool to the Beans Development Mode and your Browser inspector. It displays all of the available hooks that have been created dynamically by the Beans HTML API, making it easy for Beans Themes Developers to visualise the appropriate hook to use.

The Beans logo and Beans name are being used with kind permission from the amazing people behind the Beans Framework.

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

== Frequently Asked Questions ==
= I installed the plugin, now what? =

The Beans Visual Hook Guide doesn't have any settings as such. It adds an extra menu item to the WordPress Toolbar.

If you haven't enabled Beans Development Mode this menu item will link to the Beans setting page so that you may enable it.

Once Beans Development Mode has been enabled, you will be presented with an 'Enable Beans Visual Hook Guide' menu item, click on this and the plugin will do its thing. You will now have a drop-down menu available, from this menu you can:

*Display Action Hooks on a hook by hook basis
*Display all Action Hooks at once (Crazy Mode)
*Clear all displayed Action Hooks
*Clear all displayed Action Hooks and Disable the functionality.

= Does this plugin require the Beans Framework to work =

Yes

If a Beans Framework Child theme is not the active theme, the plugin will not activate.

Even if it did activate it would be useless.

= What happens if I deactivate the Beans Framework =

If you swap themes to a non-Beans framework theme while the plugin is active then it will just harmlessly disable itself.

= I have my admin bar disabled, can I still use this plugin? =

You can, but it is (much) more difficult. As each hook can be displayed selectively, there are many (so many) query_args used to display each hook.

When the Beans Visual Hook Guide is active your url will already have the following query_arg added:

?bvhg_enable=show

To display the 'beans_content' hook add this to end of your url:

&beans_content=show

So now it will look like the following:

?bvhg_enable=show&beans_content=show

For each hook you wish to display you need to add an ampersand followed by {markup_id}=show


== Screenshots ==
1. Display Hooks Selectively
2. Display All Hooks (so many hooks)
3. View the possible Hooks for each Markup
4. Data Markup IDs viewed in the inspector