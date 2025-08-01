'use strict';

var nhLayoutBuilder = {
	blockCode: '',
	iframe: $('#iframe-website'),
	iframeContent: null,
	blockModal: $('#layout-builder-block-modal'),
	device: 'desktop',
	stateUrl: {
		urls: [],	
		index: -1,
		action: ''
	},
	init: function(){
		var self = this;

		if(self.iframe.length == 0) return;
		if(self.blockModal.length == 0) return;		

		self.events();
		
	},
	events: function(){
		var self = this;
			
		$(document).on('click', '[nh-prev="url"]', function(e) {
			e.preventDefault();

			self.changeState('prev');
		});

		$(document).on('click', '[nh-next="url"]', function(e) {
			e.preventDefault();
			
			self.changeState('next');  			
		});

		$(document).on('click', '[nh-change-device]', function(e) {
			var device = $(this).attr('nh-change-device') || 'desktop';
			self.changeDevice(device);
		});

		self.blockModal.on('hidden.bs.modal', function () {
		  	self.blockModal.find('.modal-body').html('');
		});

		var cssFile = `<link href="${adminPath}/layout-builder/css/block.css?v=${Date.now()}" rel="stylesheet" type="text/css" />`;
		var jsFile = `<script src="${adminPath}/layout-builder/js/block.js?v=${Date.now()}" type="text/javascript" /></script>`;

		self.iframe.ready(function () {
			self.iframeContent = self.iframe.contents();
			// load file css vào iframe
			if (typeof(self.iframeContent.find('body').attr('nh-loaded-builder')) == _UNDEFINED) {
				self.iframeContent.find('body').attr('nh-loaded-builder', 1).append(cssFile);
				self.iframeContent.find('body').append(jsFile);
			}
		});

		self.iframe.on('load', function () {
			self.iframeContent = self.iframe.contents();
			// kiểm tra xem tham số có nh-device chưa
			// chưa có thì chuyển hướng lại
			var locationIframe = self.iframeContent[0].location;
			var urlParams = new URLSearchParams(locationIframe.search);
			var device = urlParams.get('nh-device') || 'desktop';
			if(device != self.device){
				locationIframe.replace(self.addParamToUrl(locationIframe.href));
				return false;
			}

			var action = self.stateUrl.action || '';
			if(action == '') self.addStateUrl(locationIframe.href);
			
			// load file css vào iframe
			if (typeof(self.iframeContent.find('body').attr('nh-loaded-builder')) == _UNDEFINED) {
				self.iframeContent.find('body').attr('nh-loaded-builder', 1).append(cssFile);
				self.iframeContent.find('body').append(jsFile);
			}

			self.iframeContent.on('click', 'a', function(e) {
				var href = $(this).attr('href');

				if(typeof(href) != _UNDEFINED && href != '#' && href != 'javascript' && href != 'javascript:;'){
					locationIframe.replace(self.addParamToUrl(href));
					return false;
				}
			});
		});
	},
	changeState: function(action = ''){
		var self = this;

		if(action == 'prev'){
			self.stateUrl.index --;
			if(self.stateUrl.index < 0) self.stateUrl.index = -1;

			self.stateUrl.action = 'prev';
		}

		if(action == 'next'){
			self.stateUrl.index ++;
			if(self.stateUrl.index > self.stateUrl.urls.length) self.stateUrl.index = self.stateUrl.urls.length;

			self.stateUrl.action = 'next';
		}		

		var urlRedirect = self.stateUrl.urls[self.stateUrl.index] || '/';
		return self.iframe[0].contentWindow.location.href = urlRedirect;
	},
	addStateUrl: function(url = ''){
		var self = this;

		self.stateUrl.index ++;
		self.stateUrl.urls.push(url);

		// clear action 
		self.stateUrl.action = '';
	},
	changeDevice: function(device = ''){
		var self = this;
		if(device != 'desktop' && device != 'mobile') return;

		var demoIframe = $('#demo-webite');
				
		self.device = device;
        switch (device) {
            case 'desktop':
                demoIframe.delay(500).attr('style', 'max-width: 100%; max-height: calc(100vh - 32px);');
                break;
            case 'mobile':
                demoIframe.delay(500).attr('style', 'max-width: 375px; max-height: 568px; border-top: 1px solid #efefef;');
                break;
        }

	    self.iframe.attr('src', self.addParamToUrl(self.iframeContent[0].location.href));
	},
	loadConigBlockModal: function(blockCode = ''){
		var self = this;
		self.blockCode = blockCode;

        KTApp.blockPage();

		self.blockModal.find('.modal-body').html('');
		nhMain.callAjax({
    		async: false,
			url: adminPath + '/layout-builder/load-config-block-modal',
			dataType: _HTML,
			data: {
				code: blockCode
			},
		}).done(function(response) {
			self.blockModal.modal('show');
			self.blockModal.find('.modal-body').html(response);
			nhBlockConfig.init();

			KTApp.unblockPage();

			if($('form#add-view-form').length > 0) {
				$('form#add-view-form').attr('action', adminPath + '/template/block/add-view' + '/' + self.blockCode);
			}

			self.logUpdate.init();
			nhViewLogFile.init();
		});
	},
	reloadContentBlockIframe: function(){
		var self = this;
		if(self.blockCode == '') return;		

		KTApp.blockPage();

		nhMain.callAjax({
    		async: false,
			url: `/block/ajax-load-content/${self.blockCode}`,
			dataType: _HTML,
			data: {
				code: self.blockCode,
				layout_builder: 1
			},
		}).done(function(response) {
			self.iframeContent.find(`[nh-block="${self.blockCode}"]`).html();
			self.iframeContent.find(`[nh-block="${self.blockCode}"]`).html(response);
			self.blockModal.modal('hide');
			KTApp.unblockPage();
		});
	},
	addParamToUrl: function(url = ''){
		var self = this;

		if(typeof(url) == _UNDEFINED || url == null || url == '#' || url == 'javascript' || url == 'javascript:;') return url;
		if(url.indexOf(window.location.origin) == -1) url = window.location.origin + url;

		var urlObject = new URL(url);
		
		urlObject.searchParams.set('nh-mode', 'layout-builder');
		urlObject.searchParams.set('nh-device', self.device);

		return urlObject.href;
	},
	
	logUpdate: {
		tabElement: null,
		wrapElement: null,
		initLoad: true,
		init: function(){
			var self = this;

			self.tabElement = $('#tab-logs');
			if(self.tabElement.length == 0) return;

			self.wrapElement = self.tabElement.find('[nh-wrap="logs"]');

			self.events();
		},
		events: function(){
			var self = this;

			$('a[data-toggle="tab"][href="#tab-logs"]').on('shown.bs.tab', function (e) {
				if(self.initLoad) self.loadLog();				
			});

			self.tabElement.on('click', '.kt-pagination .pages-link:not(.disabled)', function(e) {
                var page = $(this).data('page');
                self.loadLog(page);
            });


			self.tabElement.on('click', '[nh-log="rollback"]', function(e) {
				var log_id = $(this).data('id') || '';
				if(log_id == '') {
					toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
					return;
				}
								
				swal.fire({
			        title: nhMain.getLabel('phuc_hoi_cau_hinh'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_phuc_hoi_lai_cau_hinh_ban_ghi_nay'),
			        type: 'warning',
			        
			        confirmButtonText: '<i class="fa fa-history"></i>' + nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-warning',
			        
			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		KTApp.blockPage(blockOptions);
						nhMain.callAjax({
							url: adminPath + '/template/block/rollback-log/' + nhBlockConfig.block_code,
							data: {
								log_id: log_id
							},
						}).done(function(response) {
							KTApp.unblockPage();
							
							//show message and redirect page
						   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
				        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
				        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
				        	toastr.clear();
				            if (code == _SUCCESS) {
				            	toastr.info(message);
				            	location.reload();
				            } else {
				            	toastr.error(message);
				            }

						});
			    	}
			    });
			});
		},
		loadLog: function(page = 1){
			var self = this;

			KTApp.blockPage(blockOptions);
			nhMain.callAjax({
				url: adminPath + '/template/block/logs/' + nhBlockConfig.block_code,
				data: {
					page: page
				},
				dataType: 'html'
			}).done(function(response) {
				KTApp.unblockPage();

			   	self.tabElement.attr('nh-init', 'loaded');
			   	self.wrapElement.html(response);

			   	self.initLoad = false;
			});
		}
	}
}

$(document).ready(function() {
	nhLayoutBuilder.init();
});

