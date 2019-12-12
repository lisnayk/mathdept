<?php
defined('_JEXEC') or die;

/**
 * Template for Joomla! CMS, created with Artisteer.
 * See readme.txt for more details on how to use the template.
 */



require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';

// Create alias for $this object reference:
$document = & $this;

// Shortcut for template base url:
$templateUrl = $document->baseurl . '/templates/' . $document->template;

// Initialize $view:
$view = $this->artx = new ArtxPage($this);

// Decorate component with Artisteer style:
$view->componentWrapper();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $document->language; ?>" lang="<?php echo $document->language; ?>" dir="ltr">
<head>
 <jdoc:include type="head" />
 <link rel="stylesheet" href="<?php echo $document->baseurl; ?>/templates/system/css/system.css" type="text/css" />
 <link rel="stylesheet" href="<?php echo $document->baseurl; ?>/templates/system/css/general.css" type="text/css" />
 <link rel="stylesheet" type="text/css" href="<?php echo $templateUrl; ?>/css/template.css" media="screen" />
 <!--[if IE 6]><link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/template.ie6.css" type="text/css" media="screen" /><![endif]-->
 <!--[if IE 7]><link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/template.ie7.css" type="text/css" media="screen" /><![endif]-->
 <script type="text/javascript">if ('undefined' != typeof jQuery) document._artxJQueryBackup = jQuery;</script>
 <script type="text/javascript" src="<?php echo $templateUrl; ?>/jquery.js"></script>
 <script type="text/javascript">jQuery.noConflict();</script>
 <script type="text/javascript" src="<?php echo $templateUrl; ?>/script.js"></script>
 <script type="text/javascript">if (document._artxJQueryBackup) jQuery = document._artxJQueryBackup;</script>
</head>
<body>
<div id="art-page-background-glare-wrapper">
    <div id="art-page-background-glare"></div>
</div>
<div id="art-main">
    <div class="cleared reset-box"></div>
<?php if ($view->containsModules('position-1', 'position-28', 'position-29')) : ?>
<div class="art-bar art-nav">
<div class="art-nav-outer">
<div class="art-nav-wrapper">
<div class="art-nav-inner">
  <?php if ($view->containsModules('position-28')) : ?>
  <div class="art-hmenu-extra1"><?php echo $view->position('position-28'); ?></div>
  <?php endif; ?>
  <?php if ($view->containsModules('position-29')) : ?>
  <div class="art-hmenu-extra2"><?php echo $view->position('position-29'); ?></div>
  <?php endif; ?>
  <?php echo $view->position('position-1'); ?>
</div>
</div>
</div>
</div>
<div class="cleared reset-box"></div>
<?php endif; ?>
<div class="art-header">
<div class="art-header-position">
    <div class="art-header-wrapper">
        <div class="cleared reset-box"></div>
        <div class="art-header-inner">
<div class="art-headerobject"></div>
<div class="art-logo">
 <h1 class="art-logo-name">Математичний факультет<a href="<?php echo $document->baseurl; ?>/"><?php echo $this->params->get('siteTitle'); ?></a></h1>
 <h2 class="art-logo-text">Запорізький національний університет<?php echo $this->params->get('siteSlogan'); ?></h2>
</div>

        </div>
    </div>
</div>


</div>
<div class="cleared reset-box"></div>
<div class="art-box art-sheet">
    <div class="art-box-body art-sheet-body">
<?php echo $view->position('position-15', 'art-nostyle'); ?>
<?php echo $view->positions(array('position-16' => 33, 'position-17' => 33, 'position-18' => 34), 'art-block'); ?>
<div class="art-layout-wrapper">
    <div class="art-content-layout">
        <div class="art-content-layout-row">
<?php if ($view->containsModules('position-7', 'position-4', 'position-5')) : ?>
<div class="art-layout-cell art-sidebar1">
<?php echo $view->position('position-7', 'art-block'); ?>
<?php echo $view->position('position-4', 'art-block'); ?>
<?php echo $view->position('position-5', 'art-block'); ?>

  <div class="cleared"></div>
</div>
<?php endif; ?>
<div class="art-layout-cell art-content">

<?php
  echo $view->position('position-19', 'art-nostyle');
  if ($view->containsModules('position-2'))
    echo artxPost($view->position('position-2'));
  echo $view->positions(array('position-20' => 50, 'position-21' => 50), 'art-article');
  echo $view->position('position-12', 'art-nostyle');
  if ($view->hasMessages())
    echo artxPost('<jdoc:include type="message" />');
  echo '<jdoc:include type="component" />';
  echo $view->position('position-22', 'art-nostyle');
  echo $view->positions(array('position-23' => 50, 'position-24' => 50), 'art-article');
  echo $view->position('position-25', 'art-nostyle');
?>

  <div class="cleared"></div>
</div>

        </div>
    </div>
</div>
<div class="cleared"></div>


<?php echo $view->positions(array('position-9' => 33, 'position-10' => 33, 'position-11' => 34), 'art-block'); ?>
<?php echo $view->position('position-26', 'art-nostyle'); ?>
<div class="art-footer">
    <div class="art-footer-body">
        <?php echo $view->position('position-14'); ?>
                <div class="art-footer-text">
                    <?php if ($view->containsModules('position-27')): ?>
                    <?php echo $view->position('position-27', 'art-nostyle'); ?>
                    <?php else: ?>
                    <?php ob_start(); ?>
<p><a href="#">Link1</a> | <a href="#">Link2</a> | <a href="#">Link3</a></p><p>Copyright © 2012. All Rights Reserved.</p>

                    <?php echo str_replace('%YEAR%', date('Y'), ob_get_clean()); ?>
                    <?php endif; ?>
                </div>
        <div class="cleared"></div>
    </div>
</div>

    <div class="cleared"></div>
    </div>
</div>
<div class="cleared"></div>
<p class="art-page-footer"></p>

    <div class="cleared"></div>
</div>

<?php echo $view->position('debug'); ?>
</body>
</html>