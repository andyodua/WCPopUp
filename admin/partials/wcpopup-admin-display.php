<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       WCPopUp
 * @since      1.0.0
 *
 * @package    Wcpopup
 * @subpackage Wcpopup/admin/partials
 */
global $wpdb;
$items_per_page = 25;
$page = isset( $_GET['wcpage'] ) ? abs( (int) $_GET['wcpage'] ) : 1;
$offset = ( $page -1 ) * $items_per_page;
$table_name = $wpdb->prefix . "wcpopup_clients";
$sql = "
	SELECT 
		uid,
		date,
		name,
		country_detect,
		country_select,
		user_agent,
		ip_addr,
		phone,
		email
	FROM $table_name 
";

$total_query = "SELECT COUNT(1) FROM (${sql}) AS combined_table";
$total = $wpdb->get_var( $total_query );
$popuprecord = $wpdb->get_results($sql.' ORDER BY date DESC LIMIT '. $offset.', '. $items_per_page, OBJECT );
?>


<div class="wrap">
<h1 class="wp-heading-inline">WCPopUp Записи</h1>

<hr class="wp-header-end">


<h2 class="screen-reader-text">Фильтровать список записей</h2><ul class="subsubsub">
	<li class="all"><a href="edit.php?post_type=post" class="current" aria-current="page">Все <span class="count">(<?php echo count($popuprecord);?>)</span></a></li>
</ul>
<form id="posts-filter" method="get">

<p class="search-box">
	<label class="screen-reader-text" for="post-search-input">Поиск записей:</label>
	<input type="search" id="post-search-input" name="s" value="">
	<input type="submit" id="search-submit" class="button" value="Поиск записей"></p>

<input type="hidden" name="post_status" class="post_status_page" value="all">
<input type="hidden" name="post_type" class="post_type_page" value="post">



<input type="hidden" id="_wpnonce" name="_wpnonce" value="45644b2cd4"><input type="hidden" name="_wp_http_referer" value="/wp-admin/edit.php">	
<div class="tablenav top">

<h2 class="screen-reader-text">Список записей</h2><table class="wp-list-table widefat fixed striped posts">
	<thead>
	<tr>
		<th scope="col" id="author" class="manage-column column-author">Имя</th>
		<th scope="col" id="author" class="manage-column column-author">Страна</th>
		<th scope="col" id="categories" class="manage-column column-categories">Телефон</th>
		<th scope="col" id="tags" class="manage-column column-tags">Емаил</th>
		<th scope="col" id="tags" class="manage-column column-tags">Айпи</th>
		<th scope="col" id="date" class="manage-column column-date sortable asc">
			<a href="p?orderby=date&amp;order=desc"><span>Дата</span><span class="sorting-indicator"></span></a>
		</th>	
	</tr>
	</thead>

	<tbody id="the-list">
	<?php	 if ($popuprecord){
		foreach ($popuprecord AS $key=>$row){
		?>
		<tr id="<?php echo $row->uid;?>" class="">
			<td class="title column-title has-row-actions column-primary page-title">
				<strong>
					<a class="row-title"><?php echo $row->name;?></a>
				</strong>
			</td>
			
			<td class="column-title" >
				<?php echo $row->country_select;?> (detect:<?php echo (empty($row->country_detect)) ? "NaN": $row->country_detect;?>)
			</td>
			<td class="column-title" >
				<?php echo $row->phone;?>
			</td>
			<td class="column-title" >
				<?php echo $row->email;?>
			</td>
			<td class="column-title" >
				<?php echo $row->ip_addr;?>
			</td>
			<td class="date column-date" data-colname="Дата">
				<abbr title="<?php echo $row->date;?>"><?php echo $row->date;?></abbr>
			</td>		
		</tr>
		<?php }
		} ?>
		</tbody>



</table>
<!-- 	<div class="tablenav bottom">

				<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-bottom" class="screen-reader-text">Выберите массовое действие</label><select name="action2" id="bulk-action-selector-bottom">
<option value="-1">Действия</option>
	<option value="edit" class="hide-if-no-js">Изменить</option>
	<option value="trash">Удалить</option>
</select>
<input type="submit" id="doaction2" class="button action" value="Применить">
		</div>
				<div class="alignleft actions">
		</div>
<div class="tablenav-pages one-page"><span class="displaying-num">1 элемент</span>
<span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
<span class="screen-reader-text">Текущая страница</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">1 из <span class="total-pages">1</span></span></span>
<span class="tablenav-pages-navspan" aria-hidden="true">›</span>
<span class="tablenav-pages-navspan" aria-hidden="true">»</span></span></div>
		<br class="clear">
	</div> -->

</form>
<?php
echo paginate_links( array(
                        'base' => add_query_arg( 'wcpage', '%#%' ),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => ceil($total / $items_per_page),
                        'current' => $page
                    ));
?>
<br class="clear">
</div>
