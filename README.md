ZnoteAAC
========

### What is Znote AAC?

Znote AAC is a full-fledged website used together with an Open Tibia(OT) server. 
It aims to be super easy to install and compatible with all the popular OT distributions. 
It is created in PHP with a simple custom procedural framework. 

### Where do I download?

We use github to distribute our versions, stable are tagged as releases, while development is the latest commit. 
* [Stable] (https://github.com/Znote/ZnoteAAC/releases)
* [Developement] (https://github.com/Znote/ZnoteAAC/archive/master.zip)

**NOTE:** Developement version supports TFS 1.0, but you can expect bugs to occur. 

### Compatible OT distributions
Znote AAC primarily aims to be compatible with [Forgotten Server] (https://github.com/otland/forgottenserver)
Forgotten Server is commonly known as TFS (The Forgotten Server) and Znote AAC supports these versions:
* TFS 0.2.13+ (Since initial release)
* TFS 0.3.6+ (Since Znote AAC 1.2)
* TFS 1.2+ (Since Znote AAC 1.5)

### Requirements
* PHP Version 5.3.3 or higher. Mostly tested on 5.6 and 7.0. Most web stacks ships with this as default these days.

### Optionals
* For email registration verification and account recovery: [PHPMailer] (https://github.com/PHPMailer/PHPMailer/releases) Version 5.x, extracted and renamed to just "PHPMailer" in Znote AAC directory. 
* PHP extention curl for PHPMailer, paypal and google reCaptcha services.
* PHP extention openssl for google reCaptcha services.

### Installation instructions

1: Extract the .zip file to your web directory (Example: C:\UniServ\www\ )
Without modifying config.php, enter the website and wait for mysql connection error.
This will show you the rest of the instructions as well as the mysql schema.

2: Edit config.php and: 
- modify $config['TFSVersion'] with correct TFS version you are running. (TFS_02, TFS_03, TFS_10). 
- modify $config['page_admin_access'] with your admin account username(s).

3: Before inserting correct SQL connection details, visit the website ( http://127.0.0.1/ ), it will generate a mysql schema you should import to your OT servers database.

4: Follow the steps on the website and import the SQL schema for Znote AAC, and edit config.php with correct mysql details.

5: IF you have existing database from active OT server, enter the folder called "special" and convert the database for Znote AAC support ( http://127.0.0.1/special/ )

6: Enjoy Znote AAC. You can look around [HERE] (http://otland.net/forums/website-applications.118/) for plugins and resources to Znote AAC, for instance various free templates to use.

7: Please note that you need PHP cURL enabled to make Paypal payments work. 

8: You may need to change directory access rights of /engine/cache to allow writing.

### TODO List:
* Check [Milestones] (https://github.com/Znote/ZnoteAAC/milestones)