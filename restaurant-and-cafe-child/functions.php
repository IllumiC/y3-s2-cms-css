<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'slick-css','owl-carousel' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );

// END ENQUEUE PARENT ACTION

function show_games(){
	return '
		<input id="game-min" type="number" class="form-control" placeholder="Lowest Price">
		<br>
		<br>
		<input id="game-max" type="number" class="form-control" placeholder="Highest Price">
		<br>
		<br>
		<button id="game-search">Search</button>
		<br>
		<br>
		<span id="game-display"></span>
		<script>
			function formatDescription(game){
				var html = "";
				if (game.headline != null){
					html = html + `<h5>` + game.headline + `</h5>`;
				}
				html = html + `<p>`;
				if (game.discounted == true){
					html = html + `<s>&#36;` + game.original_price/100 + `</s><br>`;
				}
				if (game.final_price == 0){
					html = html + `Free to Play<br>`;
				}
				else{
					html = html + `&#36;` + game.final_price/100 + `<br>`;
				}
				if (game.discounted == true){
					html = html + `<small>Offer valid through ` + new Date(game.discount_expiration * 1000).toDateString() + `</small><br>`;
				}
				if (game.controller_support != null){
					html = html + `<small>With ` + game.controller_support + ` controller support</small><br>`;
				}
				html = html + `</p>`
				return html;
			}
			function updateGames(data){
				var gamesHtml = ""
				console.log(data);
				if (data.status != null){
					if (data.status != 200){
						gamesHtml = `<p><b>Critical Error!</b> ` + data.title;
					}
				}else if (data.count == 0){
					gamesHtml = `<p><b>Error!</b> ` + data.message;
				}else{
					data.data.forEach(function(game) {
						var gameHtml = `
							<div class="elementor-container elementor-column-gap-default">
								<div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-f045114 data-id="f045114" data-element_type="column">
									<div class="elementor-widget-wrap elementor-element-populated">
										<div class="elementor-element elementor-element-2d5ec3c elementor-position-left elementor-vertical-align-top elementor-widget elementor-widget-image-box" data-id="2d5ec3c" data-element_type="widget" data-widget_type="image-box.default">
											<div class="elementor-widget-container">
												<div class="elementor-image-box-wrapper">
													<figure class="elementor-image-box-img">
														<a href="https://store.steampowered.com/app/` + game.id + `">
															<img width="960" height="720" src="` + game.large_capsule_image + `" class="elementor-animation-grow attachment-full size-full" alt="" loading="lazy">
														</a>
													</figure>
													<div class="elementor-image-box-content">
														<h3 class="elementor-image-box-title">
															<a href="https://store.steampowered.com/app/` + game.id + `">
																` + game.name + `
															</a>
														</h3>
														<div class="elementor-image-box-description">
															` + formatDescription(game) + `
														</div>
													</div>
												</div>			
											</div>
										</div>
									</div>
								</div>
							</div>
						`;
						gamesHtml = gamesHtml + "<br>" + gameHtml;
					});
				}
				jQuery("#game-display").html(gamesHtml);
			}
			function requestGames(){
				var endpoint = "https://cms2021-d57043-isu3tridaa-oa.a.run.app/games"
				if (jQuery("#game-min").val().length != 0 && jQuery("#game-max").val().length != 0) endpoint = endpoint + "?minPrice=" + Math.round(jQuery("#game-min").val()*100) + "&maxPrice=" + Math.round(jQuery("#game-max").val()*100);
				else if (jQuery("#game-min").val().length != 0) endpoint = endpoint + "?minPrice=" + Math.round(jQuery("#game-min").val()*100);
				else if (jQuery("#game-max").val().length != 0) endpoint = endpoint + "?maxPrice=" + Math.round(jQuery("#game-max").val()*100);
				jQuery.ajax({
					url: endpoint,
					method: "GET",
					dataType: "json",
					success: updateGames,
					error: function (request, error, data) {
						updateGames(request.responseJSON);
					}
				});
			};
			jQuery("#game-search").on("click", requestGames);
			requestGames();
		</script>';
}

function ls_date(){
	$date = date('D M d Y');
	return '<p><small>Game information valid through '.$date.'</small></p>';
}

function ls_product_by_category($atts){
	$args = shortcode_atts(
		array(
			'category' => 'baked-goods'
		),
		$atts
	);
	$output = '
	<table class="table">
		<thead>
			<tr>
				<th scope="col">SKU</th>
				<th scope="col">Name</th>
			  	<th scope="col">Price</th>
			</tr>
		</thead>
		<tbody>
	';
	$products = wc_get_products( $args );
	foreach($products as $product){
		$output .= '
			<tr>
				<th scope="row">'.$product->get_sku().'</th>
				<td><a href="'.get_permalink($product->get_id()).'">'.$product->get_name().'</a></td>
				<td>&euro;'.number_format((float)$product->get_price(), 2, '.', '').'</td>
			</tr>
		';
	};
	$output .= '
		</tbody>
	</table>
	';
	return $output;
}

function ls_count_by_category($atts){
	$args = shortcode_atts(
		array(
			'category' => 'baked-goods'
		),
		$atts
	);
	$count = 0;
	$products = wc_get_products( $args );
	foreach($products as $product){
		$count += 1;
	};
	$plural = '';
	if ($count != 1){
		$plural = 's';
	};
	return '<p><small>'.$count.' product'.$plural.' in the '.$args["category"].' category.</small></p>';
}

function ls_delivered_by($atts){
	$args = shortcode_atts(
		array(
			'last-order' => '2pm',
			'delivered-by' => 'tomorrow afternoon'
		),
		$atts
	);
	return '<p><small>Order by '.$args["last-order"].' to have your order delivered by '.$args["delivered-by"].'.</small></p>';
}

function ls_product_by_rating($atts){
	$args = shortcode_atts(
		array(
			'minimum' => 0,
			'maximum' => 5
		),
		$atts
	);
	$output = '
	<table class="table">
		<thead>
			<tr>
				<th scope="col">SKU</th>
				<th scope="col">Name</th>
			  	<th scope="col">Price</th>
				<th scope="col">Category</th>
				<th scope="col">Rating</th>
			</tr>
		</thead>
		<tbody>
	';
	$products = wc_get_products(array("limit"=>-1));
	foreach($products as $product){
		if($product->get_average_rating()>=$args['minimum'] && $product->get_average_rating()<=$args['maximum']){
			$output .= '
				<tr>
					<th scope="row">'.$product->get_sku().'</th>
					<td><a href="'.get_permalink($product->get_id()).'">'.$product->get_name().'</a></td>
					<td>&euro;'.number_format((float)$product->get_price(), 2, '.', '').'</td>
					<td>'.$product->get_categories().'</td>
					<td>'.$product->get_average_rating().'</td>
				</tr>
			';
		};
	};
	$output .= '
		</tbody>
	</table>
	';
	return $output;
}

function ls_product_featured(){
	$output = '
	<table class="table">
		<thead>
			<tr>
				<th scope="col">SKU</th>
				<th scope="col">Name</th>
			  	<th scope="col">Price</th>
			</tr>
		</thead>
		<tbody>
	';
	$products = wc_get_products(array("limit"=>-1));
	foreach($products as $product){
		if($product->is_featured()){
			$output .= '
				<tr>
					<th scope="row">'.$product->get_sku().'</th>
					<td><a href="'.get_permalink($product->get_id()).'">'.$product->get_name().'</a></td>
					<td>&euro;'.number_format((float)$product->get_price(), 2, '.', '').'</td>
				</tr>
			';
		};
	};
	$output .= '
		</tbody>
	</table>
	';
	return $output;
}


add_shortcode("steam-games","show_games");
add_shortcode("valid-date","ls_date");
add_shortcode("product-info","ls_product_by_category");
add_shortcode("product-count","ls_count_by_category");
add_shortcode("delivered-by","ls_delivered_by");
add_shortcode("product-rating","ls_product_by_rating");
add_shortcode("product-featured","ls_product_featured");
?>