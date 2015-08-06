<script src="{$facebook_js_url}"></script>
<link href="{$facebook_css_url}" rel="stylesheet">
{if $blockcmfacebook_rows}
	<div class="bootstrap panel">
		<div class="panel-heading">
			<i class="icon-picture-o"></i> {l s='Preview' mod='blockfacebook'}
		</div>
		<div id="fb-root"></div>
		<div id="facebook_cm_block">
            <div class="fb-comments" data-width="{$blockcmfacebook_width}" data-colorscheme="{$blockcmfacebook_theme}" data-order-by="{$blockcmfacebook_orderby}" data-href="http://developers.facebook.com/docs/plugins/comments/" data-numposts="{$blockcmfacebook_rows}"></div>
		</div>
	</div>
{/if}
