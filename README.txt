Envaya is a communication and publishing platform for many independent users
with interfaces via the web, mobile web, and SMS. 

Envaya consists of a generic core framework, together with modular 
tools and applications designed specifically for civil society 
organizations in developing countries.

Envaya is designed to be easy-to-use by people with very limited computer
skills, so it generally favors simplicity over the customizability offered 
by generic content management tools such as Wordpress, Drupal, or Google Sites. 
Envaya also is designed for low-bandwidth and mobile environments, 
and provides multilingual support including content translation.

For more information about Envaya's software and design principles, see
http://envaya.org/envaya/page/software

Potential ways of using Envaya's source code may include:

* Contributing code to be used on http://envaya.org, such as new website designs, 
  or applications specific to civil society organizations.
* Adapting Envaya for a different class of users and hosting a service yourself. 
* Studying the source code for inspiration, or adapting parts of the source code 
  into unrelated projects.
* ...?

The GitHub repository https://github.com/youngj/Envaya only contains the subset of 
Envaya's source code that is open-source. Additional modules are developed as 
closed-source code and stored in separate repositories.

============
Installation
============
See INSTALL.txt

=======
License
=======
See LICENSE.txt

====================
Source Code Overview
====================
www/
    - Document root for web requests. 
        www/index.php is entry point for all PHP requests.
        www/_media/ contains static files served directly by the web server.
start.php
    - File that can be included by any PHP script to initialize the Envaya engine
make.php
    - Main build script. Minifies JavaScript and CSS, copies media files to www/_media/,
      and generates cache files in build/ .
      
      In a development environment (with 'debug' => true in config/local.php), 
      most changes don't require running make.php, except for modifying static files in _media.
runserver.php
    - Runs various daemon processes (only for development environment, not production server)
_media/
    - Static files, e.g. images and Flash. make.php copies these to www/_media/
build/
    - PHP files dynamically created by make.php
deploy/
    - Scripts for deploying code to production servers
config/
    - Configuration settings. Local settings should be defined in config/local.php
engine/
    - Autoloaded PHP classes. 
      Controllers, models, and other non-view PHP code is defined here.
js/
    - Uncompressed JavaScript code. make.php minifies them and copies to www/_media/.
lib/
    - PHP files included on every request (mostly standalone functions which can't be autoloaded)
languages/
    - Localized strings for the user interface in each supported language
schema/
    - SQL for initializing the database
scripts/
    - Miscellaneous command line scripts
test/
    - Test scripts. test/TestSuite.php is main script
themes/
    - Metadata for page themes
vendors/
    - Third-party PHP libraries and external programs 
views/
    - PHP files loaded by the view() function (engine/lib/views.php) 
      which render parts of the output.
      Organized by viewtype, 'default' is standard HTML view
mod/
    - Modules, which are a (mostly) self-contained implementation of some feature set.      
      Some modules may be stored in a separate source control repository with different licensing terms.
      
      Each module's directory structure is analogous to the top level directory structure, e.g.:
        mod/<modulename>/{_media,js,engine,config,languages,schema,test,themes,views}           
      
      Each module must contain a file located at
        mod/<modulename>/module.php 
      which defines a class Module_<modulename> that extends Module.
      This Module_<modulename> subclass defines how classes and views 
      defined in other modules/core should be modified if this module is loaded.
        
      For the most part, files in enabled modules work the same as if they were defined in
      the corresponding top level directory; e.g. it acts like each module directory tree is
      merged into the top level directory tree.

===============
Getting Started
===============
See TUTORIAL.txt for step-by-step walk-throughs about how to work with Envaya's source code.

=============
Running tests
=============

Envaya's user-interface tests are in the test/ directory. 
The tests assume that no Envaya services are currently running,
and that Firefox is installed on the local computer.

To run the tests, run the following script from the command line:

        php test/TestSuite.php
