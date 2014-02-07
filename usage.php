	<style type="text/css">
	#ipager{
		display: block;
		margin-top: 11px;
	}
	#ipager span{
		background: none repeat scroll 0 0 #FFFFFF;
		border: 1px solid #CCCCCC;
		display: block;
		float: right;
		margin-right: 4px;
		padding: 3px 7px;
		text-decoration: none;
		 box-shadow: 0 0 2px #CCCCCC;
	}
	#ipager span a{text-decoration: none;}
	#nodata{
		background: none repeat scroll 0 0 #FFFFFF;
		border: 1px solid #CCCCCC;
		padding: 3px 7px;
		text-decoration: none;
		 box-shadow: 0 0 2px #CCCCCC;
		 border-right: 3px solid red;
		 margin-top: 20px;
	}

	</style>

	<div class="wrap"><div id="icon-tools" class="icon32"></div>
	<h2>Review requests to ioin courses</h2>
	<?php 
	require_once('pagination.class.php');	
	$currentFile = "edit.php";	
	$sql = " SELECT * FROM afaq_courses ORDER BY id DESC";
	global $wpdb;
	$pager = new PS_Pagination($wpdb,$currentFile, $sql,10, 7,"&post_type=courses&page=revision-orders");
	$pager->setDebug(false);
	$result = $pager->paginate();
	$total = @$wpdb->num_rows;	
	if( $total >= 1 ) :
	?>
	<?php add_thickbox(); ?>	
	<table cellspacing="0">
	<tr>
	<th>Name</th>
	<th> Email</th>
	<th>Phone</th>
	<th>Notes</th>
	<th>Course</th>
	</tr>

	<?php foreach($result as $post ): ?>
	<tr>
	<td><?php echo $post->name; ?></td>
	<td><?php echo $post->mail; ?></td>
	<td><?php echo $post->mobile; ?></td>
	<td>
	<div id="luay-<?php echo $post->id; ?>" style="display:none;">
     <p dir="rtl" style="text-align: right">
          <?php echo $post->notes; ?>
     </p>
	</div>
	<a href="#TB_inline?width=200&height=250&inlineId=luay-<?php echo $post->id; ?>" class="thickbox">Details</a>	
	</td>
	<td><a href="post.php?post=<?php echo $post->post_id; ?>&action=edit" target="_blank"><?php echo $post->post_name; ?></a></td>
	</tr>
	<?php endforeach; ?>
	<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	</tr>	
	</table>

	<?php 
	$data = $wpdb->query($sql);
	$total =  $wpdb->num_rows;
	if($total >=10){ 
	echo '<div id="ipager">';
	echo $pager->renderFullNav();  
	echo '</div>';
	}
	?>
	<?php else: ?>	
	<div id="nodata"><p>No Orders Found!</p></div>	
	<?php endif;?>
	</div> <!-- #wrap -->