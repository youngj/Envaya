<?php
    $org = get_loggedin_user();
?>

<p>
By signing up for Envaya, your organization has a basic website with a home page. Great! 
But don't stop now, because your website can be much better.
</p>
<p>
For example, you can add other pages on topics such as your organization's history, programs, and team. You can also write news
updates and show photos from your projects. 
</p>
<p>
This page will help you learn how your Envaya website works, and how to make it great.
</p>

<h3>Contents</h3>
<ul style='font-weight:bold'>
    <li><a href='org/help#viewing'>Viewing your website</a></li>    
    <li><a href='org/help#editing'>Editing your website</a></li>
    <li><a href='org/help#settings'>Account Settings</a></li>
    <li><a href='org/help#home'>Home Page</a></li>
    <li><a href='org/help#news'>News Updates</a></li>
    <li><a href='org/help#connecting'>Connecting with Other Organizations</a></li>
</ul>

<h3 id='viewing'>Viewing your website</h3>

<p>
 The web address for your website's home page is: <br /> <strong><a href='<?php echo $org->getURL() ?>'><?php echo $org->getURL() ?></a></strong>
</p> 

<?php if (!$org->isApproved()) { ?>

<p>
Currently, only you can see your website. Before it is available to the public, Envaya needs to 
approve your organization.  After your organization is approved, 
anyone with an Internet connection will be able to see your website. 
</p>


<?php } else { ?>

<p>
Your website has already been approved by Envaya, so anyone with an Internet connection can see your website. 
</p>


<?php } ?>

<h3 id='editing'>Editing your website</h3>

<p>
Although anyone will be able to see your website, only you can edit it.
</p>

<p>
From any web browser, you can use your Envaya username <strong><?php echo $org->username ?></strong> and password
to log in to edit your website. You can share this username and password with other people in your organization who you trust
to edit your website.
</p>

<p>
The main place to edit your website is the <strong><a href='pg/dashboard'>Edit Site</a></strong> page. 
Here you can write a news update and edit any of the pages on your site.
When you are logged in, you can always get to this page by clicking the 
<a href='pg/dashboard' target='_blank'><img class='icon_with_bg' src='_graphics/pencil.gif?v2' /></a> icon at the top right.
</p>

<p>
By looking at any page's background color, you can tell whether a page is public or if it is 
only available when you are logged in. All pages with a <strong>dark gray background</strong>, such as this one, are 
only available when you are logged in.
</p>

<p>
If you are using a public computer, remember to click the <img class='icon_with_bg' src='_graphics/logout.gif?v2' /> icon to 
log out when you are done editing.
</p>

<h3 id='settings'>Account Settings</h3>

<p>
When you are logged in to Envaya, the <a href='pg/settings' target='_blank'><img class='icon_with_bg' src='_graphics/settings.gif' /></a> icon 
at the top right will take you to your account settings.
</p>

<p>On this page, you can:
</p>

<ul>
    <li>Change your password</li>
    <li>Update your email address</li>
    <li>Upload a logo to display at the top of each page of your website</li>
</ul>


<h3 id='home'>Home Page</h3>

<p>
The home page is the most important page of your website. 
When you are logged in to Envaya, the 
<a href='<?php echo $org->getURL() ?>' target='_blank'><img class='icon_with_bg' src='_graphics/home.gif?v2' /></a> icon 
at the top right will always take you to your home page.
</p>

<p>By default, the home page shows:
</p>

<ul>
    <li>Your mission statement</li>
    <li>Your three latest news updates</li>
    <li>The relevant sectors where your organization operates</li>
    <li>A map of your location.</li>
</ul>

<h3 id='news'>News Updates</h3>

<p>
Your website's <strong><a href='<?php echo $org->getURL() ?>/news'>News</a></strong> page shows all of the news updates that your organization has written. 
News updates also let you show photos from your projects. 
We encourage you to post news updates often!
</p>

<p>
News updates are a great way to let other organizations know what you're doing. On the 
<strong><a href='org/feed'>Latest news updates</a></strong> page, you can read news from other organizations that are 
doing things you're interested in. If an organization hundreds of miles away has a successful project and writes about it
on Envaya, you can read it and get ideas for your own organization!
</a>
</p>

<p>
If you don't have a web browser available, you can also add news updates in other ways:
</p>

<ul>
<li><strong>Email</strong>: Simply send an email to <strong><? echo $org->getPostEmail() ?></strong> 
with your news update in the subject or body. If you have a photo to show, just add it as an attachment.</li>
<li><strong>SMS</strong>: Coming soon, you will be able to send news updates from your phone!</li>
</ul>

<h3 id='connecting'>Connecting with Other Organizations</h3>

<p>
A major benefit of having your website on Envaya is that the network of sites makes it easy for you to find other organizations, 
and for other organizations to find you!
</p>

<p>
Write more about the partners page.
</p>
