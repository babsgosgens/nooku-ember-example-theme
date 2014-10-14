<?
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */
?>

<h4><?= translate( 'Untranslated Strings' ) ?></h4>
<pre>
<? foreach ($strings as $key => $occurance) : ?>
	<? foreach ( $occurance as $i => $info) : ?>
	<?	
		$class	= $info['class'];
		$func	= $info['function'];
		$file	= $info['file'];
		$line	= $info['line'];
	?>
	<?= strtoupper( $key )."\t$class::$func()\t[$file:$line]\n"; ?>
	<? endforeach; ?>
<? endforeach; ?>
</pre>