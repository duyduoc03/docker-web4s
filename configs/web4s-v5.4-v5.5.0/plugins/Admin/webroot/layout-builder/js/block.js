'use strict';

var nhConfigBlockFrontend = {	
	bodyIframe: null,
	fileManagerIframe: null,
	init: function(){
		var self = this;
		self.bodyIframe = $('#iframe-website').contents();
		self.fileManagerIframe = $('iframe.fancybox-iframe').contents();
		if(self.bodyIframe.find('[nh-block]').length == 0) return;

		self.events();
	},
	events: function(){
		var self = this;

		self.bodyIframe.on('mouseenter', '[nh-block]', function(e) {
			var code = $(this).attr('nh-block');
			if(typeof(code) == _UNDEFINED || !code.length > 0 || $(this).find('.nh-config-action').length > 0) return;

			var html = `
			<div class="nh-config-action">
				<a nh-show-config="${code}"href="javascript:;" class="inner-action">
					<i class="fa-light fa-gear"></i>
					${code}
				</a>
			</div>`;

			$(this).children().first().css('position', 'relative');
			$(this).children().first().append(html);
		});

		self.bodyIframe.on('mouseleave', '[nh-block]', function(e) {
			$(this).find('.nh-config-action').remove();
		});

		self.bodyIframe.on('mouseenter', 'a[target="_blank"]', function(e) {
			$(this).removeAttr('target');
		});

		self.bodyIframe.on('click', '[nh-block] [nh-show-config]', function(e) {
			var code = $(this).attr('nh-show-config') || '';
			if(code == '') return;
			nhLayoutBuilder.loadConigBlockModal(code);
		});

		self.fileManagerIframe.on('click', '[nh-wrap="list-files"] figure', function(e) {
			console.log("dahjbfsfs");
		});

	}
}

$(document).ready(function() {
	nhConfigBlockFrontend.init();
});
