=== WordPress Automatic Online Backup ===
Contributors: vladbabii
Donate link: http://wordpressbackup.com/donate/
Tags: backup, database, files, encryption, AES, 
Requires at least: 2.1
Tested up to: 2.7
Stable tag: trunk

Plugin that allows you to backup and restore your wordpress blog very easily. Requires a (free, at least ;) ) account on wordpressbackup.com

== Description ==

You can prevent loss of data using a automated backup of your blog database. You create a free account on www.wordpressbackup.com (wpb), connect your blog with the wbp via a key. 

Your data will be backed up every few hours in a sql-export format, compatible with phpMyAdmin or any other software that lets you run sql queries on MySQL servers.

If needed, data privacy is assured by two 32-character "passwords" using AES as the algorthm (you can google the term AES and find out why it's secure). These passwords are kept in your blog and are not sent to the wordpressbackup.com site. That means we won't back them up for you and we don't have acces to your data.

You can download your backups at any time. The wordpressbackup.com site will hold the last 5 mb of archived backups (equivalent of about 30-60 mb worth of unarchived exports). When a new backup is created the oldest one is erased when you go over the account storage limit. You can also login and "lock" a backup - that ensures it will never be deleted.

Need more space? Donate and you get as much space as you need.

If you have any trouble installing the plugin you can use the contact form at www.wordpressbackup.com/contact/ and someone will guide you through the process of using the plugin.

Thank you for taking the time of reading this.

== Installation ==
Short version

   1. Install the WordPressBackup Plugin (from this site or wordpress.org)
   2. Activate/enable the plugin
   3. Create a user here
   4. Add your site on the backup list
   5. Go back to what you’re doing, your data is safe.

Long version

It sounds like a lot of work but it won’t take you more than a few minutes

   1. Install the WordPressBackup Plugin (from this www.wordpressbackup.com or wordpress.org)
   2. Activate/enable the plugin
   3. You now have a new main menu in the administration area, called ‘wpBackup’, with a subpage with options
   4. Go to the options page, set or re-generate a site key 
   5. Disable or enable restoring. We suggest disabling the restore until you need it. We misclicked the restore button a few times and went ‘Ooops!’ ‘Hey, it works!’.
   6. Open http://www.wordpressbackup.com/backup/
   7. Create a new user
   8. You will receive an e-mail with an activation code
   9. Go back to Open http://www.wordpressbackup.com/backup/
  10. Click Activate account
  11. Enter your user name and activation key
  12. You will be logged in
  13. Click the Sites submenu
  14. Click ‘+Add a site’
  15. Enter a name for your site
  16. Enter the web address (like http://www.my-blog.com/ or http://www.mysie.com/blog/). Please don’t forget that the address should end with a /, like “my-blog.com/” and not “my-blog.com”
  17. Enter the wordpress key - THE KEY THAT YOU HAVE SET IN YOUR PLUGIN ON YOUR BLOG, NOT THE ACCOUNT ACTIVATION KEY YOU GOT IN THE E-MAIL WITH THE NEW ACCOUNT 
  18. Click Add it
  19. (you can click TEST to see if we can reach your site. if it doesn’t say it’s ok use the contact form and send us your account name and site and we will look into it - we usually “fix” things in about a hour)
  20. You’re done. The site will be put in the backup queue and will be backed up



== More Information ==
More information on [WordPress Backup](http://www.wordpressbackup.com/ "Backup Wordpress")
