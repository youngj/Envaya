<?php
    $img = function($filename)
    {
        return "<img style='display:block;margin:10px auto 20px auto;border:1px solid black' src='/_media/images/translate/$filename' />";
    };
    
    $section = function($section, $title)
    {
        return "<a href='/tr/instructions#{$section}'>".$title."</a>";
    };
    
    $url = abs_url("/tr");
?>
<div style='width:600px'>
<p>Envaya&apos;s Translations site, <strong><?php echo $url; ?></strong>, allows anyone to help 
translate Envaya into different languages. This page explains how to use the Translations 
site.

<h2>Contents</h2>
<ul>
    <li><?php echo $section('register','Registration'); ?></li>
    <li><?php echo $section('languages','Choosing Languages'); ?>
    <li><?php echo $section('target_language','Target Language Page'); ?></li>
    <li><?php echo $section('group','Module Page'); ?></li>
    <li><?php echo $section('key','Translating Individual Phrases'); ?></li>
    </li>
</ul>
</p>
<h2 id='register'>Registration</h2>
<p>
Anyone who wishes to contribute translations should first 
<strong><a href='/pg/register?next=/tr'>register an account</a></strong> on Envaya.
</p>
<h2 id='languages'>Choosing Languages</h2>
<p>
In the Translations site, you will be using two different languages:
<ul>
 <li>The <strong>base</strong> language: the language that you are translating <em>from</em>.</li>
 <li>The <strong>target</strong> language: the language that you are translating <em>to</em>.</li>
</ul>

By default, the Translations site uses English as the 'base' language. However, you can switch to use Kiswahili 
as the base language, by using the drop-down list at the top of the page, or the links at the bottom of the page:

<?php echo $img('main_sw.png'); ?>


<p>
The <a href='<?php echo $url; ?>'>main page</a> of the Translations site lists all the 'target' languages that are currently 
available for translation.</p>
<?php echo $img('main.png'); ?>


<p>From the main page, click the name of the target language you want to translate.</p>

<h2 id='target_language'>Target Language Page</h2>
<p>
The page for a target language lists all the modules that can be translated into that language (Kiswahili in the example below). 
It also shows the translation progress for each module (the fraction of translated text strings):
</p>

<?php echo $img('language.png'); ?>
<p>
Each module contains the text for a part of Envaya. For example, the 'home' module contains text displayed on 
<a href='<?php echo abs_url('/'); ?>'>Envaya&apos;s home page</a>, and the 'itrans' module contains the text for 
the Translations site itself.
</p>
<p>
To translate a particular module, click the name of the module that you wish to translate.
</p>

<p>
In addition, there are links on the right side of the page that allow you to see recent translations
in that language, and translation statistics and history for yourself and any other user.
</p>

<h2 id='group'>Module Page</h2>
<p>
The page for one of Envaya&apos;s modules has a list text strings in both the 'base' language and the 'target' language.
Each text string also has a unique Internal ID, which is not translated.
</p>
<?php echo $img('group.png'); ?>
<p>
Click the Internal ID of a text string to translate it into the target language.
</p>
<p>
The Filter section allows you to change which text strings are shown in the list.
To show only the strings that are currently missing a translation in the target language, choose
'Missing translation' from the drop down menu, then click Search:
<?php echo $img('filter_missing.png'); ?>
</p>
<p>
To show only the strings that contain a particular word or phrase (in either the base language, target language, or internal ID),
type the word or phrase into the box, then click Search:
<?php echo $img('filter_query.png'); ?>
</p>
<p>
After creating a filter, you can remove the filter (to see all text strings again) by
 clearing the search box, selecting 'All' from the dropdown box, then clicking Search.
</p>

<h2 id='key'>Translating Individual Phrases</h2>
<p>
When you click on one of the Internal IDs on a module&apos;s page, you arrive at a page
for that individual text string (a word or phrase).
<p>
This page that allows registered users to add new translations,
as well as to view any translations that have already been submitted.
</p>
<?php echo $img('key.png'); ?>
<p>
To add a translation, type your translation in the box, and click 'Submit translation'.
</p>
<?php echo $img('key_submitted.png'); ?>
<p>
When you add a translation, it will show your Envaya username on the left, along 
with the current score of the translation.
</p>
<h3>Translation Scores</h3>
<p>
The score for each translation is important if people have submitted multiple
translations for a word or phrase. The translation with the highest score is displayed
on the Module page, and the one with the highest score will eventually be used within Envaya.
</p>
<?php echo $img('key_votes.png'); ?>
<p>
Each user can add or subtract one point from the score of any translation (including translations
submitted by other users). Clicking the + or - buttons will add or subtract one point from a translation.
</p>
<h3>Deleting Translations</h3>
<p>
If you made a mistake in translation, simply add a new translation and then click the Delete link to delete the old translation.
You can only delete your own translations.
</p>
<h3>Placeholders</h3>
<p>
Some phrases contain <em>placeholders</em>. Placeholders are tokens
like <b>{name}</b> or <b>%s</b> that Envaya will replace with the actual values like <em>JEAN Media</em> or <em>42</em>. 
Each placeholder must appear somewhere in the translated text. If the placeholder is word, like <b>{name}</b>, that word
should <em>not</em> be translated into the target language. 
</p>
<p>
Any required placeholders will be listed below the box for adding a translation:
</p>
<?php echo $img('placeholders.png'); ?>
<h3>Navigation</h3>
<p>
After you are done translating a word or phrase, click the <strong>Next &#xbb;</strong> link to 
translate the next word or phrase in the module, or click the module name at the top of the page
('feed' in the example below) to return to the list of text strings.
</p>
<?php echo $img('navigation.png'); ?>

<h2 id='key'>Contacting Us</h2>
<p>
If you have any questions, problems, or feedback, feel free to <a href='/envaya/page/contact'>contact us</a>.
Happy translating!
</p>
</div>