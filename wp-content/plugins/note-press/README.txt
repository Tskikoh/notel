=== Note Press ===
Contributors: datainterlock
Tags: note, admin notes, developer notes, notepad, collaboration, clients
Donate link:  https://www.patreon.com/user?u=6474471
Requires at least: 3.9
Tested up to: 4.8
Stable tag: trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add, edit and delete multiple notes to multiple users and display them with icons on the Admin page or Dashboard.


== Description ==
Note Press is an extremely easy to use note system for the WordPress Admin panel. Creating to-do lists, leaving instructions for clients, collecting code snippets or collaborating with other users are just a few of the uses for Note Press. You can even give access to Note Press for your Authors, Contributors and Editors. Unlike other note plugins, Note Press keeps thing extremely simple by using features familiar to all WordPress users. Simply click on the Note Press button on the Admin Menu and view a list of all notes. Or visit the dashboard to view Sticky Notes in any color. You can even set a priority and a deadline for notes.


== Installation ==
1. Navigate to the \'Add New\' in the plugins dashboard
2. Search for \'Note Press\'
3. Click \'Install Now\'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the \'Add New\' in the plugins dashboard
2. Navigate to the \'Upload\' area
3. Select `Note_Press.zip` from your computer
4. Click \'Install Now\'
5. Activate the plugin in the Plugin dashboard

= Using HTTP =

1. Download `Note_Press.zip` from https://wordpress.org/plugins/note-press/
2. Extract the \'Note Press` directory to your computer
3. Upload the `Note Press` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard


== Frequently Asked Questions ==
= Who can see Note Press? =

It will depend on the configuration settings.  Admins can configured Note Press so that anyone with Contributor access and above can read and write notes with Note Press.
Admins and Super Admins will be able to see all notes sent to and from all users with Note Press access.
Users who don't have Note Press access can see and delete Dashboard Sticky Notes

= What's a Sticky Note?  =

Note Press will allow you to create Sticky notes on the Wordpress Admin Dashboard and allow you to chose the color of the note.  A  Sticky note will be the only way that users, like contributors who have access to the dashboard but do not have Note Press access, can see notes sent to them.

= Does Note Press support multi-site? =
Yes, but.  There is no capability to send notes across sites.  Each site will have it's own copy of Note Press activated but each site can only send note to that site's users. 

= Can the icons be replaced? =

Yes. The icons are stored in the Note_Press/public/icons directory.  To add your own custom icons, simply upload them to that directory and Note Press will find them. Be aware that Note Press is designed for 16x16 icons.  Using icons of any other size will produce unpredictable results.

= What does the search look for? =

Note Press has the ability to search through your notes.  When doing so, it checks for matches in both the title and the contents of the note.  The search is not case-sensitive.

= How many notes can Note Press hold? =

To the limit of your database. Only 5 notes will be displayed in the list at a time though.

= Can media be stored in the notes? =

Yes. Note Press uses the same editor (Tiny MCE) used when creating posts and pages.

= Can shortcodes be used in the notes? =

Yes. Although not all shortcodes have been tested, Note Press does process the contents of your notes for shortcodes.

== Screenshots ==
1. The Wordpress Dashboard showing sticky notes in different colors and with media embedded.
2. The main Note Press Main panel showing a list of notes.
3. The Note Press Settings menu
4. Adding a note to Note Press.

== Changelog ==
= 0.1.7 =
* Fixed an error where the next/prev arrows in the note list had the wrong URL.

= 0.1.6 =
* Fixed an error with database name being hard coded.
* Added limited multi-site abilities.

= 0.1.5 =
* Added the ability to send notes to other users with at least Author priviliges.
* Added the ability to post sticky notes on the dashboard in any color.
* Added the ability to allow Authors and above to access Note Press and create their own notes and edit/delete those sent to them.
* Gave priviliges to Admin and Super Admin users to view, edit and delete any note.
* Added the ability to see when notes have been viewed.
* Added the ability for recipients of Sticky Notes to delete them from the dashboard.
* Added Nonce check on input forms.

= 0.1.4 =
* Patch to fix install issues with 0.1.3

= 0.1.3 =
* Boilerplate updated.
* Public funcitons moved to admin menu where they should have been in the first place.
* Fixed strings that were hard coded instead of being in the language files.
* Removed language files that prevented language switching.
* Added list of notes to the dashboard.
* Database updated with new fields.
* Fixed column sorting which was broken in 0.1.2 as a result of preparing queries.

= 0.1.2 =
* Security update to reduce risk of SQL injection.

= 0.1.1 =
* Updated to comply with depreciated functions.
* Removed some annoying notices of variables not being set.

= 0.1.0 =
* Initial Beta Release

== Upgrade Notice ==