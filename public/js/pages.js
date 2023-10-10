var onLoad = function() {
	var pageName = window.location.pathname.split('/').pop().split('?').shift().replace(/\.(php|htm|html)$/,'');

	$('[data-toggle="tooltip"]').tooltip();

	if (pageName !== 'login') {
		$('[data-plugin-colorpicker]').each(function() {
			var pluginOptions = $(this).data('plugin-options');
			if (!pluginOptions) pluginOptions = {};
			pluginOptions.format = 'hex';
			pluginOptions.format = 'rgba';
			$(this).themePluginColorPicker((pluginOptions) ? pluginOptions : {});
			$(this).on('changeColor', function (event) {
				rgb = event.color.toRGB();
				console.log(rgb);
				rgbStr = 'rgba('+rgb.r+','+rgb.g+','+rgb.b+','+rgb.a+')';
				$(this).find("input").val(event.color.toHex());
				$(this).find("input").val(rgbStr);
			});
		});

		$('[data-plugin-datepicker]').each(function() {
			var pluginOptions = $(this).data('plugin-options');
			$(this).themePluginDatePicker((pluginOptions) ? pluginOptions : {});
		});

		$('[data-plugin-masked-input]').each(function() {
			var pluginOptions = $(this).data('plugin-options');
			$(this).themePluginMaskedInput((pluginOptions) ? pluginOptions : {});
		});

		$('[data-plugin-maxlength]').each(function() {
			var pluginOptions = $(this).data('plugin-options');
			$(this).themePluginMaxLength((pluginOptions) ? pluginOptions : {});
		});

		$('[data-plugin-multiselect]').each(function() {
			var pluginOptions = $(this).data('plugin-options');
			$(this).themePluginMultiSelect((pluginOptions) ? pluginOptions : {});
		});

		$('[data-plugin-textarea-autosize]').each(function() {
			var pluginOptions = $(this).data('plugin-options');
			$(this).themePluginTextAreaAutoSize((pluginOptions) ? pluginOptions : {});
		});

		$('[data-plugin-timepicker]').each(function() {
			var pluginOptions = $(this).data('plugin-options');
			$(this).themePluginTimePicker((pluginOptions) ? pluginOptions : {});
		});

		$('[data-plugin-toggle]').each(function() {
			var pluginOptions = $(this).data('plugin-options');
			$(this).themePluginToggle((pluginOptions) ? pluginOptions : {});
		});

		$('[data-toggle-class][data-target]').each(function() {
			toggleClass( $(this) );
		});

		$('body').tooltip({
			selector: '[data-toggle=tooltip],[rel=tooltip]',
			container: 'body',
			trigger: 'hover'
		});

		$('[data-toggle=popover]').popover();
		$('.profile-img').initial({ fontSize : 58 });

		$('input.fonts').fontselect().change(function(){
			var font = $(this).val().replace(/\+/g, ' ');
		});

		var maskUrl = function(id) {
			return str_replace(
				[ '00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50','51','52','53' ],
				[ 'a','A','b','B','c','C','d','D','e','E','f','F','g','G','h','H','i','I','j','J','k','K','l','L','m','M','n','N','o','O','p','P','q','Q','r','R','s','S','t','T','u','U','v','V','w','W','x','X','y','Y','z','Z','-','_' ],
				id
			);
		}
	}

	var randWidgetDomain = function() {
		var domains = ['linksnap.net', 'widget-code.com', 'widget-host.com', 'top-widgets.com', 'jscloudcdn.com'];
		return domains[Math.floor(Math.random()*domains.length)];
	}

	var datatablesTimescaleCb = function (nRow, aaData, iStart, iEnd, aiDisplay) {
		var count = 0;
		var visitors = 0
		var clicks = 0
		var downloads = 0
		var conv = 0
		var avgCPA = 0
		var epc = 0
		var payout = 0
		var download_payout = 0;
		var ref_payout = 0;
		var currency = '$';

        for ( var i=0 ; i<aaData.length ; i++ )
        {
            visitors += parseInt(aaData[i][1].replace(',', '')) || 0;
			clicks += parseInt(aaData[i][2].replace(',', '')) || 0;
			downloads += parseInt(aaData[i][3].replace(',', '')) || 0;
			conv += parseFloat(aaData[i][4]) || 0;
			avgCPA += parseFloat(aaData[i][5].substring(1)) || 0;
			epc += parseFloat(aaData[i][6].substring(1)) || 0;
			payout += parseFloat(aaData[i][7].replace(/,/g, '').substring(1)) || 0;
			download_payout += parseFloat(aaData[i][8].replace(/,/g, '').substring(1)) || 0;
			ref_payout += parseFloat(aaData[i][9].replace(/,/g, '').substring(1)) || 0;
			currency = aaData[i][7].substring(0,1);
			count++;
        }

		payout = Math.round(payout*100)/100;
		conv = (clicks == 0) ? 0 : Math.round((downloads/clicks)*10000)/100;
		avgCPA = (downloads == 0) ? 0 : Math.round((payout/downloads)*100)/100;
		epc = (clicks == 0) ? 0 : Math.round((payout/clicks)*100)/100;
		download_payout = Math.round(download_payout*100)/100;
		ref_payout = Math.round(ref_payout*100)/100;

		var nCells = nRow.getElementsByTagName('th');
        nCells[1].innerHTML = visitors.toLocaleString('en');
		nCells[2].innerHTML = clicks.toLocaleString('en');
		nCells[3].innerHTML = downloads.toLocaleString('en');
		nCells[4].innerHTML = conv.toLocaleString('en') + '%';
		nCells[5].innerHTML = currency + avgCPA.toLocaleString('en');
		nCells[6].innerHTML = currency + epc.toLocaleString('en');
		nCells[7].innerHTML = currency + payout.toLocaleString('en');
		nCells[8].innerHTML = currency + download_payout.toLocaleString('en');
		nCells[9].innerHTML = currency + ref_payout.toLocaleString('en');
    }

    var datatablesCb = function (nRow, aaData, iStart, iEnd, aiDisplay) {
		var count = 0;
		var visitors = 0
		var clicks = 0
		var downloads = 0
		var conv = 0
		var avgCPA = 0
		var epc = 0
		var payout = 0
		var currency = '$';

        for ( var i=0 ; i<aaData.length ; i++ )
        {
            visitors += parseInt(aaData[i][1].replace(',', '')) || 0;
			clicks += parseInt(aaData[i][2].replace(',', '')) || 0;
			downloads += parseInt(aaData[i][3].replace(',', '')) || 0;
			conv += parseFloat(aaData[i][4]) || 0;
			avgCPA += parseFloat(aaData[i][5].substring(1)) || 0;
			epc += parseFloat(aaData[i][6].substring(1)) || 0;
			payout += parseFloat(aaData[i][7].replace(/,/g, '').substring(1)) || 0;
			currency = aaData[i][7].substring(0,1);
			count++;
        }

		conv = (clicks == 0) ? 0 : Math.round((downloads/clicks)*10000)/100;
		avgCPA = (downloads == 0) ? 0 : Math.round((payout/downloads)*100)/100;
		epc = (clicks == 0) ? 0 : Math.round((payout/clicks)*100)/100;
		payout = Math.round(payout*100)/100;

		var nCells = nRow.getElementsByTagName('th');
        nCells[1].innerHTML = visitors.toLocaleString('en');
		nCells[2].innerHTML = clicks.toLocaleString('en');
		nCells[3].innerHTML = downloads.toLocaleString('en');
		nCells[4].innerHTML = conv.toLocaleString('en') + '%';
		nCells[5].innerHTML = currency + avgCPA.toLocaleString('en');
		nCells[6].innerHTML = currency + epc.toLocaleString('en');
		nCells[7].innerHTML = currency + payout.toLocaleString('en');
    }

    var datatablesReferralCb = function (nRow, aaData, iStart, iEnd, aiDisplay) {
		var count = 0;
		var downloads = 0
		var payout = 0
		var currency = '$';

        for ( var i=0 ; i<aaData.length ; i++ )
        {
			downloads += parseInt(aaData[i][1].replace(',', '')) || 0;
			payout += parseFloat(aaData[i][2].substring(1).replace(',', '')) || 0;
			currency = aaData[i][2].substring(0,1);
			count++;
        }

		payout = Math.round(payout*100)/100;

		var nCells = nRow.getElementsByTagName('th');
        nCells[1].innerHTML = downloads.toLocaleString('en');
		nCells[2].innerHTML = currency + payout.toLocaleString('en');
    }

	switch(pageName) {
		case 'account_profile': //{
			$('#profile-alert-vol-slider').slider({
				max : 100,
				min : 1,
				value: (uiOptions && uiOptions['sound_volume'] ? uiOptions['sound_volume'] : 50),
				slide: function(event, ui) {
					$('#profile-alert-vol').text(ui.value + '%');
				}
			});

			$('#update_profile_submit').on('click', function()
			{
				console.log("!");
				hideAlerts('inner_main');

				if (empty($('#current_password').val()))
				{
					innerMainMessage('error_div', 'error', 'You must enter your current password in order to update your profile');
					return false;
				}

				var panel = $(this).closest('.panel');
				panel.addClass('loading-mask');

				$.post('data.php?cmd=update_profile',
					{
						'email'            : $('#email').val() ,
						'current_password' : $('#current_password').val() ,
						'first_name'       : $('#first_name').val() ,
						'middle_name'      : $('#middle_name').val() ,
						'last_name'        : $('#last_name').val() ,
						'street_address'   : $('#street_address').val() ,
						'street_address_2' : $('#street_address_2').val() ,
						'city'             : $('#city').val() ,
						'state'            : $('#state').val() ,
						'country'          : $('#country').val() ,
						'zip'              : $('#zip').val() ,
						'password'         : $('#password').val() ,
						'password_confirm' : $('#password_confirm').val()
					},
					function(data)
					{
						panel.removeClass('loading-mask');

						if (data.status == 'error')
						{
							innerMainMessage('error_div', 'error', data.data);
						}
						else if (data.status == 'ok')
						{
							innerMainMessage('error_div', 'success', 'Your profile has been successfully updated');
						}
					},
					'json'
				);

				return false;
			});

			$('#saveUiSettings').on('click', function()
			{
				hideAlerts('inner_main');

				var panel = $(this).closest('.panel');
				panel.addClass('loading-mask');

				$.post('data.php?cmd=update_ui_options',
					{
						'sound_enable'   : ($('#opt-soundNotification').is(':checked') ? '1' : '0') ,
						'sound_volume'   : $('#profile-alert-vol-slider').slider('option', 'value') ,
						'webkit_enable'  : ($('#opt-chromeNotification').is(':checked') ? '1' : '0') ,
						'webkit_timeout' : $('#opt-notificationTimeout').val() ,
						'threshold'      : $('#opt-notificationThreshold').val()
					},
					function(data)
					{
						panel.removeClass('loading-mask');

						if (data.status == 'error')
						{
							innerMainMessage('ui_error_div', 'error', data.data);
						}
						else if (data.status == 'ok')
						{
							innerMainMessage('ui_error_div', 'success', 'Your user interface options have been saved');

							uiOptions['sound_enabled']        = ($('#notify_sound_enable').is(':checked') ? '1' : '0');
							uiOptions['notification_enabled'] = ($('#notify_webkit_enable').is(':checked') ? '1' : '0');
							uiOptions['sound_volume']         = $('#notify_sound_volume').val();
							uiOptions['notification_timeout'] = $('#notify_webkit_timeout').val();
							uiOptions['threshold']            = $('#notify_threshold').val();
						}
					},
					'json'
				);

				return false;
			});

		break; //}
		case 'account_reversals': //{

			$('#acc-reversals').dataTable({ "order": [[ 3, "desc" ]] });

		break; //}
		case 'account_french_offers': //{

			var dt = $('#french-offer-apps').dataTable({ "order": [[ 3, "asc" ],[4, "desc" ]] });

			$("#submit_offer_app").click(function () {
				var _this = this;
				var panel = $("section.panel-submit-app");
				panel.addClass("loading-mask");

				$.post('data.php?cmd=submit_offer_app', {
					urls: $("#offer_app_urls").val()
				}, function (data) {
					panel.removeClass('loading-mask');
					$(_this).closest("section").find("div.alert.dismissible").slideUp();
					if (data.status == 'error') {
						innerMainMessage('msg_div', 'error', data.data);
					} else if (data.status == 'ok') {
						$("#no-apps").hide();
						$("#apps-tbl").show();

						dt.fnAddData($(data.data));
						$("#offer_app_urls").val('');
						innerMainMessage('msg_div', 'info', 'Your application has been submitted. You will receive an alert when it has been reviewed by our advertisers.');
					}
				}, 'json');
			});

		break; //}
		case 'account_warnings': //{

			$('#acc-warnings').dataTable({ "order": [[0, "desc"]]});

		break; //}
		case 'dashboard': //{

			//$('#stat-1')['sparkline']([10,8,9,3,5,8,5,10,8,9,3,5,8,5,10,8,9,3,5,8,5,10,8,9,3,5,8,5], {
			$('#stat-1')['sparkline']([10,8,9,2,3,5,3,6,8,9,3,5,8,5,10,8,9,3,5,8,5,8,2,5,3,7,1,9,9,3,5,8,10,9], {
				type: 'bar',
				height: '100%',
				width: '100%',
				barColor: 'rgba(0,0,0,0.2)',
				borderColor: 'rgba(0,0,0,0.2)'
			});

			$('#stat-2')['sparkline']([10,8,9,3,5,8,5,7], {
				height: '100%',
				width: '100%',
				lineColor: 'rgba(255,255,255,0.5)',
				fillColor: 'rgba(0,0,0,0.2)',
				minSpotColor: false,
				maxSpotColor: false,
				spotColor: '#fff',
				spotRadius: 3
			});

			$('#stat-3')['sparkline']([8,4,0,0,0,0,1,4,4,10,10,10,10,0,0,0,4,6,5,9,10], {
				height: '100%',
				width: '100%',
				fillColor: false,
				lineColor: 'rgba(0,0,0,0.4)',
				//lineWidth: 4,
				lineWidth: 3,
				changeRangeMin: 0,
				chartRangeMax: 10,
				spotColor: 'rgba(0,0,0,0)',
				minSpotColor: 'rgba(0,0,0,0)',
				maxSpotColor: 'rgba(0,0,0,0)',
				highlightSpotColor: 'rgba(0,0,0,0)',
				highlightLineColor: 'rgba(0,0,0,0)'
			});

			$('#stat-3')['sparkline']([4, 1, 5, 7, 9, 9, 8, 7, 6, 6, 4, 7, 8, 4, 3, 2, 2, 5, 6, 7], {
				height: '100%',
				width: '100%',
				composite: true,
				fillColor: false,
				lineColor: 'rgba(0,0,0,0.2)',
				//lineWidth: 4,
				lineWidth: 3,
				changeRangeMin: 0,
				chartRangeMax: 10,
				spotColor: 'rgba(0,0,0,0)',
				minSpotColor: 'rgba(0,0,0,0)',
				maxSpotColor: 'rgba(0,0,0,0)',
				highlightSpotColor: 'rgba(0,0,0,0)',
				highlightLineColor: 'rgba(0,0,0,0)'
			});

			// Heatmap
			var heatmapPanel = $("#dash-heatmap");
			heatmapPanel.addClass('loading-mask-clear');
			$.get('data.php',
			{
				cmd: 'heatmap_stats'
			}, function (data) {
				heatmapPanel.removeClass('loading-mask-clear');
				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				if (data.data.constructor !== Array || data.data.length > 0) {
					$('#dash-heatmap').vectorMap({
						map: 'world_mill',
						backgroundColor: '#fff',
						zoomMax: 5,
						series: {
							regions: [{
								values: data.data,
								scale: ['#C8EEFF', '#0071A4'],
								scale: ['#0BA8E3', '#4EC6F3'],
								scale: ['#60CCF6', '#08A9E5'],
								normalizeFunction: 'polynomial'
							}]
						},
						regionStyle: {
							initial: {
								fill: '#F2FBFF',
								'fill-opacity': 1,
								stroke: '#777',
								'stroke-width': 0.2,
								'stroke-opacity': 1
							},
							hover: {
								stroke: '#000',
								'stroke-width': 0.5
							}
						},
						onRegionTipShow: function(e, el, code){
							el.html(el.html() + '<br>Visitors: ' + ((typeof data.data[code] === 'undefined') ? '0' : data.data[code]));
						}
					});
				} else {
					$("#dash-heatmap").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any traffic for today.</strong></div>");
					$("#dash-heatmap").css("height", "inherit");
				}
			}, 'json');

			// Line graph
			var linegraphPanel = $("div.stats-main");
			linegraphPanel.addClass('loading-mask-clear');
			$.get('data.php',
			{
				cmd: 'graph_dash_stats'
			}, function (data) {
				$("#range-select").show();
				linegraphPanel.removeClass('loading-mask-clear');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}
				var dashData = data.data;

				var dashOverview = MTA.Line({
					element: 'morrisArea',
					data: dashData.today,
					resize: true,
					xkey: 'time',
					parseTime: false,
					ykeys: ['v', 'c', 'l', 'r'],
					lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
					labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
					hideHover: true,
					hoverCallback: function (index, options, content, row) {
						return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
					}
				}, false);

				$('#dash-date-selector').themePluginMultiSelect().on('change', function() {
					dashOverview.toggleUpdateData();
					dashOverview.setData(dashData[$(this).val()]);
					$('.dash-overviewOpts input[type="checkbox"]').prop('checked', true);
				});

				$('.dash-overviewOpts span').on('click', function() {
					var checkBox = $(this).find('input[type=checkbox]');
					checkBox.prop('checked', !checkBox.prop('checked'));
					dashOverview.toggleYKeys([$(this).data('stat')]);
				});
			}, 'json');

			// File pie chart
			var filePanel = $("div#filebreakdown");
			filePanel.addClass('loading-mask-clear');
			$.get('data.php',
			{
				cmd: 'file_chart_stats',
			}, function (data) {
				filePanel.removeClass('loading-mask-clear');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}
				var graphData = data.data;
				if (graphData.length > 0) {
					Morris.Donut({
						resize: true,
						element: 'dash-filebreakdown',
						data: graphData,
						colors: ['#31A60E', '#33AA10', '#35AD11', '#38B113', '#3AB515', '#3CB917', '#3EBC18', '#40C01A', '#42C41C', '#45C81E', '#47CB1F', '#49CF21'],
						formatter: function (y) { return 'Leads: ' + y }
					});
				} else {
					$("#dash-filebreakdown").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any leads for today.</strong></div>");
					$("#dash-filebreakdown").css("height", "inherit");
				}
			}, 'json');

			// Detailed stats link
			$("span.dash-stat-more").click(function () {
				$("ul.nav-main > li:eq(2) > ul").slideDown(function () {
					$("ul.nav-main > li:eq(0)").removeClass("nav-active");
					$("ul.nav-main > li:eq(2) > ul > li:eq(0)").addClass("nav-active");
				});
				$("#detailed-stats-link")[0].click();
			});

		break; //}
		case 'login': //{

			var UNREAD_NEWS = 0;
			var AFFILIATE_NAME = 0
			var AFFILIATE_IS_PREMIER = false;

			window.onloadCallback = function() {
				console.log("invoked captcha callback");
				window.reCaptcha = grecaptcha.render('captcha', {
				  'sitekey' : '6Lc7JSYTAAAAAFuLSUb7zzNwsqhR8YhK6xva5ktg'
				});
			};

			var type = 'login';

			$(document).on('click', '#link_forgot_user', function(e){
				e.preventDefault();
   				hideAlerts();
				$('form').attr('id', 'forgot_user');
				$('#login_inputs').slideUp();
				$('#forgot_pass_inputs').slideUp();
				$('.login_reset').slideUp();
				$('#login_reset_back').slideDown();
				$('#forgot_user_inputs').slideDown();
				$('.login-header > h2').text('Forgot Username');
				$('.login-header > p').slideUp();
				$('button[type="submit"]').text('Send Username');
				$('button[type="submit"]').attr('id', 'forgotUserForm');
			});

			$(document).on('click', '#link_forgot_pass', function(e){
				e.preventDefault();
   				hideAlerts();
				$('form').attr('id', 'forgot_pass');
				$('#login_inputs').slideUp();
				$('#forgot_user_inputs').slideUp();
				$('.login_reset').slideUp();
				$('#login_reset_back').slideDown();
				$('#forgot_pass_inputs').slideDown();
				$('.login-header > h2').text('Forgot Password');
				$('.login-header > p').slideUp();
				$('button[type="submit"]').text('Reset Password');
				$('button[type="submit"]').attr('id', 'forgotPassForm');
			});

			$(document).on('click', '#login_reset_back', function(e){
				e.preventDefault();
   				hideAlerts();
				$('form').attr('id', 'login');
				$('#login_reset_back').slideUp();
				$('#login_inputs').slideUp();
				$('#forgot_user_inputs').slideUp();
				$('#forgot_pass_inputs').slideUp();
				$('#login_inputs').slideDown();
				$('.login_reset').slideDown();
				$('.login-header > h2').text('Welcome to ShareCash');
				$('.login-header > p').slideDown();
				$('button[type="submit"]').text('Login');
				$('button[type="submit"]').attr('id', 'loginForm');
			});

			// Handle forgot pass form
			$(document).on('click', '#forgotPassForm', function(e){
   				hideAlerts();

				if (!$('#forgot_pass_user').val() && !$('#forgot_pass_email').val()) {
					loginMessage('error', 'Please enter either your username or email.');
					return false;
				}

				var panel = $("section.body-login:eq(0)");
				panel.addClass("loading-mask");

				$.post('data.php?cmd=forgot_pw', {
						'user'  : $('#forgot_pass_user').val() ,
						'email' : $('#forgot_pass_email').val() ,
					},
					function(data) {
						panel.removeClass('loading-mask');
						if (data.status == 'error') {
							loginMessage('error', data.data);
						} else if (data.status == 'ok') {
							loginMessage('success', 'We have sent instructions on how to proceed to your email address.');
						}
					},
					'json'
				);

				return false;
			});

			// Handle forgot user form
			$(document).on('click', '#forgotUserForm', function(e){
				hideAlerts();

				if (!$('#forgot_user_email').val()) {
					loginMessage('error', 'Please enter your email.');
					return false;
				}

				var panel = $("section.body-login:eq(0)");
				panel.addClass("loading-mask");

				$.post('data.php?cmd=forgot_user', {
						'email' : $('#forgot_user_email').val()
					},
					function(data) {
						panel.removeClass('loading-mask');

						if (data.status == 'error') {
							loginMessage('error', data.data);
						} else if (data.status == 'ok') {
							loginMessage('success', 'We have sent a reminder to your email address.');
						}
					},
					'json'
				);

				return false;
			});

			// Handle login form
			$(document).on('click', '.modal-dialog #login_submit', function(e){
				hideAlerts();

				if (!$('.modal-dialog #talav_type_user_user_login_username').val() || !$('.modal-dialog #talav_type_user_user_login_password').val()) {
					loginMessage('error', 'Please enter your username and password.');
					return false;
				}

				var panel = $("section.body-login:eq(0)");
				panel.addClass("loading-mask");

//				$.post('data.php?cmd=login', {
				$.post('login', {
				    'talav_type_user_user_login': {
				        'username'     : $('.modal-dialog #talav_type_user_user_login_username').val() ,
                        'password'     : $('.modal-dialog #talav_type_user_user_login_password').val() ,
                        '_csrf_token'  : $('.modal-dialog #talav_type_user_user_login__csrf_token').val() ,
//                        'captcha'        : grecaptcha.getResponse(reCaptcha)
				    }
				},
					function(data) {
						panel.removeClass('loading-mask');

						if (data.status == 'error') {
							loginMessage('error', data.data);
							grecaptcha.reset();
						} else if (data.status == 'bad_captcha')  {
							loginMessage('error', 'Invalid CAPTCHA, please try again.');
						} else if (data.status == 'ok') {
							window.location = 'dashboard.php?c=' + Math.random();
						}
					},
					'json'
				);

				return false;
			});

		break; //}
		case 'library_ebooks': //{

			/*if (window.location.search.indexOf('cat=') === -1) {
				modalOpen($('#ebooksOverview'), true, true);
			}*/

			$(".content-item").hover(function() {
				$(this).find(".cat-file-stats").fadeIn("fast");
				$(this).find(".cat-title-blue").removeClass(".cat-title-blue").addClass("cat-title-green");
			}, function() {
				$(this).find(".cat-file-stats").fadeOut("fast");
				$(this).find(".cat-title-blue").removeClass("cat-title-green").addClass("cat-title-blue");

			});

			$('#lib-ebooks').dataTable();

			/*$(document).on('click', '.ebook-get-file', function(e){
				var tr = $(this).parent().parent();
				alert('Download ebook ID: #' + tr.data('ebookid'));
			});

			$(document).on('click', '.ebook-add', function(e){
				var tr = $(this).parent().parent();
				alert('Add ebook ID: #' + tr.data('ebookid'));
			});*/

			$(document).on('click', '.ebook-get-desc', function(e){
				var tr = $(this).parent().parent();
				$('#ebookSelectTitle').text(tr.find('td:first').text());
				$('#ebookSelectDesc').text(tr.data('ebookdesc'));
				modalOpen($('#ebookDescription'), false);
			});

			$(document).on('click', '.ebook-get-file', function()
			{
				hideAlerts('div_msg');
				var panel = $(this).closest('.panel');
				panel.addClass('loading-mask');

				var thisButton = $(this);

				thisButton.attr('disabled', 'disabled');

				$.post('data.php?cmd=ebook_download',
					{
						'ebook_id' : $(this).closest("tr").attr('data-ebook-id')
					},
					function(data)
					{
						panel.removeClass('loading-mask');

						if (data.status == 'error')
						{
							innerMainMessage('div_msg', 'error', data.data);
						}
						else if (data.status == 'ok')
						{
							window.location = data.link;
						}
					},
					'json'
				);

				return false;
			});

			$(document).on('click', '.ebook-add', function()
			{
				hideAlerts('div_msg');
				var panel = $(this).closest('.panel');
				panel.addClass('loading-mask');

				var thisButton = $(this);

				if (thisButton.attr('disabled') == 'disabled')
					return;

				thisButton.attr('disabled', 'disabled');

				$.post('data.php?cmd=ebook_add',
					{
						'ebook_id' : $(this).closest("tr").attr('data-ebook-id')
					},
					function(data)
					{
						panel.removeClass('loading-mask');

						if (data.status == 'error')
						{
							mainMessage('error', data.data);
						}
						else if (data.status == 'ok')
						{
							innerMainMessage('div_msg', 'success', 'The ebook was added to your account. You can find it in your File Manager.');
							thisButton.attr('title', 'This file has already been added to your account.');
							thisButton.find("i.fa").removeClass("fa-plus").addClass('fa-ban').css("color", "#999")
						}

						thisButton.removeAttr('disabled');
					},
					'json'
				);

				return false;
			});

		break; //}
		case 'library_software': //{

			/*if (window.location.search.indexOf('cat=') === -1) {
				modalOpen($('#ebooksOverview'), true, true);
			}*/

			$("header.page-header h2").text($("header.page-header h2").text() + " - " + $("#catName").val());


			$(".content-item").hover(function() {
				$(this).find(".cat-file-stats").fadeIn("fast");
				$(this).find(".cat-title-blue").removeClass(".cat-title-blue").addClass("cat-title-green");
			}, function() {
				$(this).find(".cat-file-stats").fadeOut("fast");
				$(this).find(".cat-title-blue").removeClass("cat-title-green").addClass("cat-title-blue");

			});

			$('#lib-software').dataTable({order: [[3, "desc"]]});

			/*$(document).on('click', '.software-get-file', function(e){
				var tr = $(this).parent().parent();
				alert('Download software ID: #' + tr.data('softwareid'));
			});

			$(document).on('click', '.software-add', function(e){
				var tr = $(this).parent().parent();
				alert('Add software ID: #' + tr.data('softwareid'));
			});*/

			$(document).on('click', '.software-get-desc', function(e){
				var tr = $(this).parent().parent();
				$('#softwareSelectTitle').text(tr.find('td:first').text());
				$('#softwareSelectDesc').text(tr.data('software-desc'));
				modalOpen($('#softwareDescription'), false);
			});

			$(document).on('click', '.software-get-file', function()
			{
				hideAlerts('div_msg');
				var panel = $(this).closest('.panel');
				panel.addClass('loading-mask');

				var thisButton = $(this);

				thisButton.attr('disabled', 'disabled');

				$.post('data.php?cmd=software_download',
					{
						'software_id' : $(this).closest("tr").attr('data-software-id')
					},
					function(data)
					{
						panel.removeClass('loading-mask');

						if (data.status == 'error')
						{
							innerMainMessage('div_msg', 'error', data.data);
						}
						else if (data.status == 'ok')
						{
							window.location = data.link;
						}
					},
					'json'
				);

				return false;
			});

			$(document).on('click', '.software-add', function()
			{
				hideAlerts('div_msg');
				var panel = $(this).closest('.panel');
				panel.addClass('loading-mask');

				var thisButton = $(this);

				if (thisButton.attr('disabled') == 'disabled')
					return;

				thisButton.attr('disabled', 'disabled');

				$.post('data.php?cmd=software_add',
					{
						'software_id' : $(this).closest("tr").attr('data-software-id')
					},
					function(data)
					{
						panel.removeClass('loading-mask');

						if (data.status == 'error')
						{
							mainMessage('error', data.data);
						}
						else if (data.status == 'ok')
						{
							innerMainMessage('div_msg', 'success', 'The software was added to your account. You can find it in your File Manager.');
							thisButton.attr('title', 'This file has already been added to your account.');
							thisButton.find("i.fa").removeClass("fa-plus").addClass('fa-ban').css("color", "#999")
						}

						thisButton.removeAttr('disabled');
					},
					'json'
				);

				return false;
			});

		break; //}
		case 'library_ebooks_pages': //{

			$(document).on('click', '.ebook-landing-url', function(e){
				var tr = $(this).parent().parent();
				$('#ebook-landing-url-link').val($("#randDomain").val()+'0'+maskUrl(tr.data('ebookpageid')));
				modalOpen($('#ebookLandingURL'));
			});

			$('#lib-ebook-landing').on('draw.dt', function() {
				$('.ebook-landing-del').confirmation({
					placement: 'top',
					onConfirm: function() {
						var ebookPageId = $(this)[0].ebookpageId;

						hideAlerts('msg_div');
						var panel = $('section.lp-panel');
						panel.addClass('loading-mask');

						$.post('data.php?cmd=landing_delete',
							{
								'id' : ebookPageId
							},
							function(data)
							{
								panel.removeClass('loading-mask');

								if (data.status == 'error')
								{
									innerMainMessage('msg_div', 'error', data.data);
								}
								else if (data.status == 'ok')
								{
									var row = $("tr[data-ebookpageid=" + ebookPageId + "]");
									dt.fnDeleteRow(row, null, true);
									dt.fnDraw(false);
								}
							},
							'json'
						);
					}
				});
			});
			var dt = $('#lib-ebook-landing').dataTable({ "order": [[ 0, "asc" ]] });

		break; //}
		case 'library_software': //{

			if (window.location.search.indexOf('cat=') === -1) {
				modalOpen($('#softwareOverview'), true, true);
			}

			$(".content-item").hover(function() {
				$(this).find(".cat-file-stats").fadeIn("fast");
			}, function() {
				$(this).find(".cat-file-stats").fadeOut("fast");
			});

			$('#lib-software').dataTable();

			$(document).on('click', '.software-get-file', function(e){
				var tr = $(this).parent().parent();
				alert('Download file ID: #' + tr.data('softwareid'));
			});

			$(document).on('click', '.software-add', function(e){
				var tr = $(this).parent().parent();
				alert('Add file ID: #' + tr.data('softwareid'));
			});

			$(document).on('click', '.software-get-desc', function(e){
				var tr = $(this).parent().parent();
				$('#softwareSelectTitle').text(tr.find('td:first').text());
				$('#softwareSelectDesc').text(tr.data('softwaredesc'));
				modalOpen($('#softwareDescription'), false);
			});

		break; //}
		case 'payments_history': //{

			(function() {
				["#paid-payments", "#pending-payments"].forEach(function (selector) {
					var $table = $(selector);

					var fnFormatDetails = function( datatable, tr ) {
						return '<pre class="m-none">' + $(tr).data('payment-info').replace(/\\n/g, "\n") + '</pre>';
					};

					var th = document.createElement('th');
					var td = document.createElement('td');
					th.innerHTML = '<i class="fa fa-info-circle"></i>';
					th.className = 'text-center';
					th.style = 'padding-right:0 !important;padding-left:0;';
					td.innerHTML = '<i data-toggle class="fa fa-plus-square-o text-primary h5 m-none" style="cursor: pointer;"></i>';
					td.className = 'text-center';

					$table.find('thead tr').each(function() {
						this.insertBefore(th, this.childNodes[0]);
					});

					$table.find('tbody tr').each(function() {
						this.insertBefore(td.cloneNode(true), this.childNodes[0]);
					});

					var datatable = $table.dataTable({
						aoColumnDefs: [{
							bSortable: false,
							aTargets: [ 0 ]
						}],
						aaSorting: [
							[4, 'desc']
						]
					});

					$table.on('click', 'i[data-toggle]', function() {
						var $this = $(this),
							tr = $(this).closest('tr').get(0);

						if ( datatable.fnIsOpen(tr) ) {
							$this.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
							datatable.fnClose(tr);
						} else {
							$this.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
							datatable.fnOpen(tr, fnFormatDetails(datatable, tr), 'details');
						}
					});
				});
			})();

		break; //}
		case 'payments_revise': //{
			var reviseRequest = true;
		case 'payments_request':
			if (!reviseRequest)
				var reviseRequest = false;

			$('.payout_check_country').change(function()
			{
				if ($('.payout_check_country').val() == 'US')
				{
					$('#show_check_phone_number').slideDown();
					$('#show_check_bank_account_number').slideDown();
				}
				else
				{
					$('#show_check_phone_number').slideUp();
					$('#show_check_bank_account_number').slideUp();

					$("#check_phone_number").val("");
					$("#check_bank_account_number").val("");
				}
			});

			$('.payout_bank_country').change(function()
			{
				switch($('.payout_bank_country').val())
				{
					case 'AU':
						$('#show_bank_au').slideDown();
						$('#show_bank_ca').slideUp();
						$('#show_bank_gb').slideUp();
						$('#show_bank_in').slideUp();

						$('#bank_sort_code').val('');
						$('#bank_transit_code').val('');
						$('#bank_state_branch_code').val('');
						$('#india_financial_system_code').val('');
					break;
					case 'CA':
						$('#show_bank_au').slideUp();
						$('#show_bank_ca').slideDown();
						$('#show_bank_gb').slideUp();
						$('#show_bank_in').slideUp();

						$('#bank_sort_code').val('');
						$('#bank_transit_code').val('');
						$('#bank_state_branch_code').val('');
						$('#india_financial_system_code').val('');
					break;
					case 'GB':
						$('#show_bank_au').slideUp();
						$('#show_bank_ca').slideUp();
						$('#show_bank_gb').slideDown();
						$('#show_bank_in').slideUp();

						$('#bank_sort_code').val('');
						$('#bank_transit_code').val('');
						$('#bank_state_branch_code').val('');
						$('#india_financial_system_code').val('');
					break;
					case 'IN':
						$('#show_bank_au').slideUp();
						$('#show_bank_ca').slideUp();
						$('#show_bank_gb').slideUp();
						$('#show_bank_in').slideDown();

						$('#bank_sort_code').val('');
						$('#bank_transit_code').val('');
						$('#bank_state_branch_code').val('');
						$('#india_financial_system_code').val('');
					break;
					default:
						$('#show_bank_au').slideUp();
						$('#show_bank_ca').slideUp();
						$('#show_bank_gb').slideUp();
						$('#show_bank_in').slideUp();

						$('#bank_sort_code').val('');
						$('#bank_transit_code').val('');
						$('#bank_state_branch_code').val('');
						$('#india_financial_system_code').val('');
				}
			});

			$('#request_payout_submit').on('click', function()
			{
				hideAlerts('inner_main');

				if ((!reviseRequest && empty($('#payout_amount').val())) || empty($('#methods_select').val()))
				{
					console.log(reviseRequest);
					console.log($('#payout_amount').val());
					console.log($('#methods_select').val());

					innerMainMessage('request_payout_alerts', 'error', 'You must complete the entire form.');
					return false;
				}

				var panel = $(this).closest('.panel');
				panel.addClass('loading-mask');

				var reqAmt = (reviseRequest ? null : $('#payout_amount').val());
				var invoiceId = (reviseRequest ? $("#invoice_id").val() : null);
				var endPoint = (reviseRequest ? 'data.php?cmd=revise_payout' : 'data.php?cmd=request_payout');
				$.post(endPoint,
					{
						'methods_select'                    : $('#methods_select').val() ,
						'payout_amount'                     : reqAmt,
						'invoice_id'					    : invoiceId,
						//'pp_email'                          : $('#pp_email').val() ,
						'ap_email'                          : $('#ap_email').val() ,
						/*'pq_email'                          : $('#pq_email').val() ,*/
						'paxum_email'						: $('#paxum_email').val() ,
						'check_full_name'                   : $('#check_full_name').val() ,
						'check_street_address'              : $('#check_street_address').val() ,
						'check_city'                        : $('#check_city').val() ,
						'check_state'                       : $('#check_state').val() ,
						'check_zip'                         : $('#check_zip').val() ,
						'check_country'                     : $('#check_country').val() ,
						'check_phone_number'                : $('#check_phone_number').val() ,
						'check_bank_account_number'         : $('#check_bank_account_number').val() ,
						'first_and_middle_name'             : $('#first_and_middle_name').val() ,
						'last_name'                         : $('#last_name').val() ,
						'street_address'                    : $('#street_address').val() ,
						'city'                              : $('#city').val() ,
						'state'                             : $('#state').val() ,
						'zip'                               : $('#zip').val() ,
						'country'                           : $('#country').val() ,
						'full_bank_name'                    : $('#full_bank_name').val() ,
						'bank_street_address'               : $('#bank_street_address').val() ,
						'bank_city'                         : $('#bank_city').val() ,
						'bank_state'                        : $('#bank_state').val() ,
						'bank_zip'                          : $('#bank_zip').val() ,
						'bank_country'                      : $('#bank_country').val() ,
						'bank_account_or_IBAN_number'       : $('#bank_account_or_IBAN_number').val() ,
						'bank_routing_number_or_SWIFT_code' : $('#bank_routing_number_or_SWIFT_code').val() ,
						'bank_sort_code'                    : $('#bank_sort_code').val() ,
						'bank_transit_code'                 : $('#bank_transit_code').val() ,
						'bank_state_branch_code'            : $('#bank_state_branch_code').val() ,
						'india_financial_system_code'       : $('#india_financial_system_code').val()
					},
					function(data)
					{
						panel.removeClass('loading-mask');

						if (data.status == 'error')
						{
							innerMainMessage('request_payout_alerts', 'error', data.data);
						}
						else if (data.status == 'ok')
						{
							if (reviseRequest) {
								innerMainMessage('request_payout_alerts', 'success', 'Your payout revision has been received, and will be processed on your next scheduled payment date.');
							} else {
								innerMainMessage('request_payout_alerts', 'success', 'Your payout has been requested and will be sent by the date shown below.');
							}

							if (!reviseRequest) {
								$('#available_payout').text(  '$' + (Math.round((substr($('#available_payout').text(), 1) - str_replace('$', '', $('#payout_amount').val()))*100)/100) );
							}
						}
					},
					'json'
				);

				return false;
			});
		break; //}
		case 'referrals_banner_generator': //{
			$('#ref-banner-gen').on('draw.dt', function() {
				$('a.livebanner-del').confirmation({
					placement: 'top',
					onConfirm: function() {
						var bannerId = $(this)[0].livebannerId;
						hideAlerts('main');

						var panel = $("section.banners-panel");
						panel.addClass('loading-mask');

						$.post('data.php?cmd=signature_delete',
							{
								'id' : bannerId
							},
							function(data)
							{
								panel.removeClass('loading-mask');

								if (data.status == 'error')
								{
									mainMessage('error', data.data);
								}
								else if (data.status == 'ok')
								{
									var row = $('td').filter(function(){
									    return $(this).text() == bannerId;
									});
									dt.fnDeleteRow(row, null, true);
									dt.fnDraw(false);
								}
							},
							'json'
						);
					}
				});
			});
			var dt = $('#ref-banner-gen').dataTable({ "order": [[ 0, "asc" ]] });

			function displaySigCode(banner_id) {
				$("#refImg").attr('src', 'http://sig.sharecash.org/' + banner_id + '.png');
				$('#livebanner-img-url').val('http://sig.sharecash.org/' + banner_id + '.png');
				$('#livebanner-html').val('<a href="http://shareca.sh/r/"><img src="http://sig.sharecash.org/' + banner_id + '.png"></a>');
				$('#livebanner-bb').val('[url=http://shareca.sh/r/][img]http://sig.sharecash.org/' + banner_id + '.png[/img][/url]');

				modalOpen($('#referralStatsBannerCode'));
			}

			$(document).on('click', 'a.livebanner-view', function(e){
				var banner_id = $(this).data('livebanner-hash');
				displaySigCode(banner_id);
			});

			$('#signature_create').on('click',function()
			{
				hideAlerts('inner_main');

				var panel = $(this).closest('.panel');
				panel.addClass('loading-mask');

				$.post('data.php?cmd=signature_create',
				{
					'text' : $('#signature_text').val() ,
					'stat' : $('#signature_statistic').val()
				},
				function(data)
				{
					panel.removeClass('loading-mask');
					$("#signature_text").val('');

					if (data.status == 'error')
					{
						innerMainMessage('signature_create_content', 'error', data.data);
					}
					else if (data.status == 'ok')
					{
						displaySigCode(data.hash, true);
						dt.fnAddData($(data.html));
					}
				},
				'json'
				);

				return false;
			});
		break; //}
		case 'referrals_banners': //{

			var refLink = $("#ref_link").val();
			$(document).on('click', 'a[data-banner]', function(e){
				var banner_id = $(this).data('banner');

				$('#banner-img-url').val('http://b.sharecash.org/' + banner_id + '.gif');
				$('#banner-html').val('<a href="' + refLink + '"><img src="http://b.sharecash.org/' + banner_id + '.gif"></a>');
				$('#banner-bb').val('[url=' + refLink + '][img]http://b.sharecash.org/' + banner_id + '.gif[/img][/url]');

				modalOpen($('#referralBannerCode'));
			});

		break; //}
		case 'referrals_overview': //{

			$('#ref-list').dataTable({ "order": [[ 2, "desc" ], [1, "desc"]] });

		break; //}
		case 'statistics_overview': //{
			// Heatmap
			var heatmapPanel = $("#dash-heatmap");
			heatmapPanel.addClass('loading-mask-clear');
			$.get('data.php',
			{
				cmd: 'heatmap_stats',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				heatmapPanel.removeClass('loading-mask-clear');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				if (data.data.constructor !== Array || data.data.length > 0) {
					$('#dash-heatmap').vectorMap({
						map: 'world_mill',
						backgroundColor: '#fff',
						zoomMax: 5,
						series: {
							regions: [{
								values: data.data,
								scale: ['#C8EEFF', '#0071A4'],
								scale: ['#0BA8E3', '#4EC6F3'],
								scale: ['#60CCF6', '#08A9E5'],
								normalizeFunction: 'polynomial'
							}]
						},
						regionStyle: {
							initial: {
								fill: '#F2FBFF',
								'fill-opacity': 1,
								stroke: '#777',
								'stroke-width': 0.2,
								'stroke-opacity': 1
							},
							hover: {
								stroke: '#000',
								'stroke-width': 0.5
							}
						},
						onRegionTipShow: function(e, el, code){
							el.html(el.html() + '<br>Visitors: ' + ((typeof data.data[code] === 'undefined') ? '0' : data.data[code]));
						}
					});
				} else {
					$("#dash-heatmap").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any traffic in this time range.</strong></div>");
					$("#dash-heatmap").css("height", "inherit");
				}

			}, 'json');

			// Line graph
			var linegraphPanel = $("div.stats-main");
			linegraphPanel.addClass('loading-mask-clear');
			$.get('data.php',
			{
				cmd: 'graph_stats',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				linegraphPanel.removeClass('loading-mask-clear');
				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var dashOverview = MTA.Line({
					element: 'morrisArea',
					data: data.data,
					resize: true,
					xkey: 'time',
					parseTime: false,
					ykeys: ['v', 'c', 'l', 'r'],
					lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
					labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
					hideHover: true,
					hoverCallback: function (index, options, content, row) {
						return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
					}
				}, false);

				$('.dash-overviewOpts span').on('click', function() {
					var checkBox = $(this).find('input[type=checkbox]');
					checkBox.prop('checked', !checkBox.prop('checked'));
					dashOverview.toggleYKeys([$(this).data('stat')]);
				});
			}, 'json');

			// File pie chart
			var filePanel = $("div#filebreakdown");
			filePanel.addClass('loading-mask-clear');
			$.get('data.php',
			{
				cmd: 'file_chart_stats',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				filePanel.removeClass('loading-mask-clear');
				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}
				var graphData = data.data;
				if (graphData.length > 0) {
					Morris.Donut({
						resize: true,
						element: 'dash-filebreakdown',
						data: graphData,
						colors: ['#31A60E', '#33AA10', '#35AD11', '#38B113', '#3AB515', '#3CB917', '#3EBC18', '#40C01A', '#42C41C', '#45C81E', '#47CB1F', '#49CF21'],
						formatter: function (y) { return 'Leads: ' + y }
					});
				} else {
					$("#dash-filebreakdown").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any leads in this time range.</strong></div>");
					$("#dash-filebreakdown").css("height", "inherit");
				}
			}, 'json');

			// Referrer domain chart
			var trafficPanel = $("div#trafficbreakdown");
			trafficPanel.addClass('loading-mask-clear');
			$.get('data.php',
			{
				cmd: 'referrer_domain_chart_stats',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				trafficPanel.removeClass('loading-mask-clear');
				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}
				var graphData = data.data;
				if (graphData.length > 0) {
					Morris.Donut({
						resize: true,
						element: 'dash-trafficbreakdown',
						data: graphData,
						colors: ['#31A60E', '#33AA10', '#35AD11', '#38B113', '#3AB515', '#3CB917', '#3EBC18', '#40C01A', '#42C41C', '#45C81E', '#47CB1F', '#49CF21'],
						formatter: function (y) { return 'Leads: ' + y }
					});
				} else {
					$("#dash-trafficbreakdown").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any leads in this time range.</strong></div>");
					$("#dash-trafficbreakdown").css("height", "inherit");
				}
			}, 'json');

			// Referral pie chart
			var referrerPanel = $("div#referralsbreakdown");
			referrerPanel.addClass('loading-mask-clear');
			$.get('data.php',
			{
				cmd: 'referral_chart_stats',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				referrerPanel.removeClass('loading-mask-clear');
				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}
				var graphData = data.data;
				if (graphData.length > 0) {
					console.log(graphData);
					Morris.Donut({
						resize: true,
						element: 'dash-referralsbreakdown',
						data: graphData,
						colors: ['#31A60E', '#33AA10', '#35AD11', '#38B113', '#3AB515', '#3CB917', '#3EBC18', '#40C01A', '#42C41C', '#45C81E', '#47CB1F', '#49CF21'],
						formatter: function (y) { return '$' + Number(y).toFixed(2) }
					});
				} else {
					$("#dash-referralsbreakdown").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any active referrals in this time range.</strong></div>");
					$("#dash-referralsbreakdown").css("height", "inherit");
				}
			}, 'json');

		break; //}
		case 'statistics_breakdown': //{
			var dashOverview = MTA.Line({
				element: 'morrisArea',
				data: [
					{time: '1:00 AM', v: 150, c: 90, l: 20, r: '15.82'},
					{time: '2:00 AM', v: 175, c: 65, l: 40, r: '35.54'},
					{time: '3:00 AM', v: 100, c: 40, l: 20, r: '18.09'},
					{time: '4:00 AM', v: 125, c: 65, l: 30, r: '25.50'},
					{time: '5:00 AM', v: 90, c: 40, l: 20, r: '17.93'},
					{time: '6:00 AM', v: 35, c: 25, l: 10, r: '5.35'},
					{time: '7:00 AM', v: 100, c: 40, l: 20, r: '12.45'}
				],
				resize: true,
				xkey: 'time',
				parseTime: false,
				ykeys: ['v', 'c', 'l', 'r'],
				lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
				labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
				hideHover: true,
				hoverCallback: function (index, options, content, row) {
					return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
				}
			}, false);

			$('.dash-overviewOpts span').on('click', function() {
				var checkBox = $(this).find('input[type=checkbox]');
				checkBox.prop('checked', !checkBox.prop('checked'));
				dashOverview.toggleYKeys([$(this).data('stat')]);
			});

			$('#datatable-default').dataTable();

		break; //}
		case 'statistics_breakdown_file': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'file',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = Morris.Bar({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						stacked: true,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>There are no files with traffic in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 1, "desc" ]],
				fnFooterCallback: datatablesCb
			});

		break; //}
		case 'statistics_breakdown_daily': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'daily',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = MTA.Line({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any traffic in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 0, "desc" ]],
				fnFooterCallback: datatablesTimescaleCb
			});

		break; //}
		case 'statistics_breakdown_daily_file': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'daily_file',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts'),
				file_id: $("input[name=fileId]").val()
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = MTA.Line({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any traffic in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 0, "desc" ]],
				fnFooterCallback: datatablesCb
			});

		break; //}
		case 'statistics_breakdown_daily_link_locker': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'daily_link_locker',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts'),
				ll_id: $("input[name=ll_id]").val()
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = MTA.Line({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any traffic in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 0, "desc" ]],
				fnFooterCallback: datatablesCb
			});

		break; //}
		case 'statistics_breakdown_daily_widget': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'daily_widget',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts'),
				widget_id: $("input[name=widget_id]").val()
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = MTA.Line({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any traffic in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 0, "desc" ]],
				fnFooterCallback: datatablesCb
			});

		break; //}
		case 'statistics_breakdown_hourly': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'hourly',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = MTA.Line({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any traffic in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 0, "desc" ]],
				fnFooterCallback: datatablesTimescaleCb
			});

		break; //}
		case 'statistics_breakdown_monthly': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'monthly',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = MTA.Line({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any traffic in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 0, "desc" ]],
				fnFooterCallback: datatablesTimescaleCb
			});

		break; //}
		case 'statistics_breakdown_country': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'heatmap_stats',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				if (data.data.constructor !== Array || data.data.length > 0) {
					$('#dash-heatmap').vectorMap({
						map: 'world_mill',
						backgroundColor: '#fff',
						zoomMax: 5,
						series: {
							regions: [{
								values: data.data,
								scale: ['#C8EEFF', '#0071A4'],
								scale: ['#0BA8E3', '#4EC6F3'],
								scale: ['#60CCF6', '#08A9E5'],
								normalizeFunction: 'polynomial'
							}]
						},
						regionStyle: {
							initial: {
								fill: '#F2FBFF',
								'fill-opacity': 1,
								stroke: '#777',
								'stroke-width': 0.2,
								'stroke-opacity': 1
							},
							hover: {
								stroke: '#000',
								'stroke-width': 0.5
							}
						},
						onRegionTipShow: function(e, el, code){
							el.html(el.html() + '<br>Visitors: ' + ((typeof data.data[code] === 'undefined') ? '0' : data.data[code]));
						}
					});
				} else {
					$("#dash-heatmap").html("<div class=\"alert alert-info mb-none\"><strong>You have no traffic in this time range.</strong></div>");
					$("#dash-heatmap").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 1, "desc" ]],
				fnFooterCallback: datatablesCb
			});

		break; //}
		case 'statistics_breakdown_referrer_domain': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'referrer_domain',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = Morris.Bar({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						stacked: true,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>You have no traffic in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 1, "desc" ]],
				fnFooterCallback: datatablesCb
			});

		break; //}
		case 'statistics_breakdown_mirror_domain': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'mirror_domain',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = Morris.Bar({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						stacked: true,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>You have no traffic in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 1, "desc" ]],
				fnFooterCallback: datatablesCb
			});

		break; //}
		case 'statistics_breakdown_widgets': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'widgets',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = Morris.Bar({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						stacked: true,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>You have no traffic in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 1, "desc" ]],
				fnFooterCallback: datatablesCb
			});

		break; //}
		case 'statistics_breakdown_link_lockers': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'link_lockers',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = Morris.Bar({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						stacked: true,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>You have no traffic in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 1, "desc" ]],
				fnFooterCallback: datatablesCb
			});

		break; //}
		case 'statistics_breakdown_ebook_lp': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'ebook_lp',
				lpId: $("#lpId").val(),
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = MTA.Line({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any traffic in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 0, "desc" ]],
				fnFooterCallback: datatablesCb
			});

		break; //}
		case 'statistics_breakdown_referral_ids': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'referral_ids',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = Morris.Bar({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						stacked: true,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>You have no active referrals in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 1, "desc" ]],
				fnFooterCallback: datatablesReferralCb
			});

		break; //}
		case 'statistics_breakdown_referral_daily': //{
			var panel = $("div.stats-main");
			panel.addClass('loading-mask');

			$.get('data.php',
			{
				cmd: 'graph_stats',
				type: 'referral_daily',
				from: $('#dateRangeForm').data('start-ts'),
				to: $('#dateRangeForm').data('end-ts')
			}, function (data) {
				panel.removeClass('loading-mask');

				if (data.status == 'error') {
					return mainMessage('error', data.data);
				}

				var graphData = data.data;
				if (graphData.length > 0) {
					var dashOverview = MTA.Line({
						element: 'morrisArea',
						data: graphData,
						resize: true,
						xkey: 'time',
						parseTime: false,
						ykeys: ['v', 'c', 'l', 'r'],
						lineColors: ['#4DC5F9', '#E9573F', '#70BA63', '#FEB252'],
						labels: ['Visitors', 'Clicks', 'Leads', 'Revenue'],
						hideHover: true,
						hoverCallback: function (index, options, content, row) {
							return content.replace(/<div class='morris-hover-point(.*?)>\s+(.*?)\s+-\s+<\/div>/g, '').replace(/Revenue:\s+/i, 'Revenue: $').replace(/\$([0-9]+)\.([0-9])\s+/g,'$$$1.$20');
						}
					}, false);

					$('.dash-overviewOpts span').on('click', function() {
						var checkBox = $(this).find('input[type=checkbox]');
						checkBox.prop('checked', !checkBox.prop('checked'));
						dashOverview.toggleYKeys([$(this).data('stat')]);
					});
				} else {
					$("#morrisArea").html("<div class=\"alert alert-info mb-none\"><strong>You don't have any traffic in this time range.</strong></div>");
					$("#morrisArea").css("height", "inherit");
				}
			}, 'json');

			$('#datatable-default').dataTable({
				"order": [[ 1, "desc" ]],
				fnFooterCallback: datatablesReferralCb
			});

		break; //}
		case 'tools_files_upload': //{

			// Don't show the modal on mobile browsers because some can't see the whole thing
			if (!(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))) {
				modalOpen($('#uploadWarning'), true, true);
			}
			$('.ftp-resetpassword').confirmation({
				placement: 'top',
				onConfirm: function() {
					alert('Reset password');
				}
			});

		break; //}
		case 'tools_files_upload': //{

			modalOpen($('#uploadWarning'), true, true);
			$('.ftp-resetpassword').confirmation({
				placement: 'top',
				onConfirm: function() {
					alert('Reset password');
				}
			});

		break; //}
		case 'tools_files_manage': //{

			var f = $('#finder').elfinder({
				url : 'scripts/filemanager/connector.php',
				lang : 'en',
				places : '',
				rememberLastDir: false,
				toolbar : [
					['back', 'reload'],
					['select', 'open'],
					['mkdir'],
					['info', 'rename', 'copy', 'paste', 'rm'],
					['icons', 'list']
				],
				contextmenu : {
					'cwd'   : ['reload', 'delim', 'mkdir', 'delim', 'cut', 'paste'],
					'file'  : ['select', 'delim', 'info', 'mirror', 'delim', 'cut', 'rm', 'rename'],
					'group' : ['select', 'delim', 'info', 'delim', 'cut', 'rm']
				},
				docked : true,
			});

		break; //}
		case 'tools_widget_edit': //{
		case 'tools_widget_create':

			$('.slider-for').slick({
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
				fade: true,
				asNavFor: '.slider-nav',
				waitForAnimate: false
			});

//			var skinSelect = $('.slider-nav').slick({
			window.skinSelect = $('.slider-nav').slick({
				slidesToShow: 3,
				slidesToScroll: 1,
				asNavFor: '.slider-for',
				dots: false,
				centerMode: true,
				infinite: true,
				variableWidth : true,
				focusOnSelect: true,
				waitForAnimate: false
			});

			$(document).on('change', '#skin_design', function(){
				if ($(this).val() === 'simple') {
					skinSelect.resize();
				}
			});

			var skinPreset = function(preset) {
				var preset = eval('(' + preset + ')');

				$.each(preset, function(i, v){
					var elm = $('#' + i);

					if (elm.length) {
						elm.attr('data-orig-val', elm.val());
						elm.val(v).trigger('change');
					}
				});
			};

			var resetPreset = function() {
				$('[data-orig-val]').each(function(){
					$(this).val($(this).data('orig-val')).trigger('change');
					$(this).removeData('orig-val');
				});
			};

			$(document).on('click', '.slider-nav .slick-slide', function() {
				resetPreset();
				var imgTag = $(this).find('img');

				if (imgTag.data('skin-preset')) {
					skinPreset(imgTag.data('skin-preset'));
				}

				$('input#skin_preset_id').val(imgTag.data('skin-id')).trigger('change');
			});

			$(document).on('click', 'button.slick-arrow', function() {
				resetPreset();
				var imgTag = $('.slider-nav .slick-current img');

				if (imgTag.data('skin-preset')) {
					skinPreset(imgTag.data('skin-preset'));
				}

				$('input#skin_preset_id').val(imgTag.data('skin-id')).trigger('change');
			});

			$('#widget-create').find('ul.pager li.finish').on('click', function(e) {
				e.preventDefault();

				$('.form-wizard .tab-pane.active').find('select, input, textarea').each(validateHandle);
				var check = $('.has-error');
				if (check.length) {
					check.focus();
					return false;
				}

				var data = {};
				$.each($('form#widget-create-form').find('input,select,textarea'), function(i,v){
					var input = $(v);
					data[input.attr('id')] = input.val();
				});

				data['version'] = 3;
				console.log(data);

				var panel = $('#widget-create .panel-body');
				panel.addClass('loading-mask');

				$.post('data.php?cmd=' + ( (pageName === 'tools_widget_create') ? 'widgets_create' : 'widgets_edit' ), data, function(data) {
					panel.removeClass('loading-mask');

					if ($('#tools_widget_create_alerts > .alert').length) {
						$('#tools_widget_create_alerts > .alert').slideUp();
					}

					if (data.status == 'error')
					{
						innerMainMessage('tools_widget_create_alerts', 'error', data.data);
						panel.removeClass('loading-mask');
					}
					else if (data.status == 'ok')
					{
						var wid = data.data;
						var randDomain = randWidgetDomain();
						$('#widget-manage-instant-code').val('<script src="https://' + randDomain + '/' + wid + '.js"></script><script>\'undefined\'==typeof ld&&(window.location=\'https://' + randDomain + '/error/block\');</script><noscript><meta http-equiv="refresh" content="0;url=https://' + randDomain + '/error/nojs"/></noscript>');
						$('#widget-manage-event-code').val('<script src="https://' + randDomain + '/event.js"></script><script>\'undefined\'==typeof ld&&(window.location=\'https://' + randDomain + '/error/block\');</script><noscript><meta http-equiv="refresh" content="0;url=https://' + randDomain + '/error/nojs"/></noscript>');
						$('#widget-manage-event-func').val('start_widget(' + wid + ');');
						$('.widgets-manage-wid').text(wid);
						modalOpen($('#widgetManageCode'), false);
					}
				}, 'json');
			});

			$('#widget-create').bootstrapWizard({
				tabClass: 'wizard-steps',
				nextSelector: 'ul.pager li.next',
				previousSelector: 'ul.pager li.previous',
				firstSelector: null,
				lastSelector: null,
				onNext: function( tab, navigation, index, newindex ) {
					$('.form-wizard .tab-pane.active').find('select, input, textarea').each(validateHandle);
					var check = $('.has-error');
					if (check.length) {
						check.focus();
						return false;
					}
				},
				onTabClick: function( tab, navigation, index, newindex ) {
					if ( newindex == index + 1 ) {
						return this.onNext( tab, navigation, index, newindex);
					} else if ( newindex > index + 1 ) {
						return false;
					} else {
						return true;
					}
				},
				onTabChange: function( tab, navigation, index, newindex ) {
					var $total = navigation.find('li').size() - 1;
					$('#widget-create').find('ul.pager li.finish')[ newindex != $total ? 'addClass' : 'removeClass' ]( 'hidden' );
					$('#widget-create').find(this.nextSelector)[ newindex == $total ? 'addClass' : 'removeClass' ]( 'hidden' );
				},
				onTabShow: function( tab, navigation, index ) {
					var $total = navigation.find('li').length - 1;
					var $current = index;
					var $percent = Math.floor(( $current / $total ) * 100);
					$('#widget-create').find('.progress-indicator').css({ 'width': $percent + '%' });
					tab.prevAll().addClass('completed');
					tab.nextAll().removeClass('completed');

					if (tab.html().indexOf('mobile') !== -1) {
						$('a[href="#widget-create-preview-mobile"]').click();
					}
				}
			});

			var updatePreview = function(e) {
				console.log("loading preview");
				e.preventDefault();

				var desktop = $('#widget-preview-wrap');
				var mobile = $('.phone-preview-frame').contents();

				var setGradient = function(elm, dir, start, end, mob) {
					var selector = (mob === true) ? mobile : desktop ;
					selector.find(elm).css('background-image', 'none');

					switch (dir) {
						case 'vertical':
							selector.find(elm).css('background', 'linear-gradient(to bottom, ' + start + ' 0%,' + end + ' 100%)');
						break;
						case 'horizontal':
							selector.find(elm).css('background', 'linear-gradient(to right, ' + start + ' 0%,' + end + ' 100%)');
						break;
						case 'diagonal-tobot':
							selector.find(elm).css('background', 'linear-gradient(135deg, ' + start + ' 0%,' + end + ' 100%)');
						break;
						case 'diagonal-totop':
							selector.find(elm).css('background', 'linear-gradient(45deg, ' + start + ' 0%,' + end + ' 100%)');
						break;
						case 'radial':
							selector.find(elm).css('background', 'radial-gradient(ellipse at center, ' + start + ' 0%,' + end + ' 100%)');
						break;
					}
				};

				var parseFont = function(font) {
					var fontWeight = 'normal';
					var selectedFont = font.replace(/\+/g,' ');
					var parsedFont = selectedFont.match(/^(.*?):([a-z0-9]+)$/i);
					if (parsedFont) {
						selectedFont = parsedFont[1];
						fontWeight = parsedFont[2];
					}
					return { font : selectedFont , weight : fontWeight };
				};

				switch ($(this).attr('id')) {
					// Skin Design
					// --------------------------------------------------
					case 'skin_design':
						// Don't trigger if we're in the initial edit process
						if (typeof EDIT_WIDGET !== 'undefined' && EDIT_WIDGET !== false) {
							return;
						}

						resetPreset();
						switch($(this).val()) {
							case 'simple':
								$('#skin_preset_id').trigger('change');
								$('#widget-preview-inner').css('width', '539px');
								$('#widget-preview-inner').css('height', '264px');
								$('#widget-preview-positioning').css('width', '539px');
								$('#widget-preview-positioning').css('height', '264px');
								$('#widget-preview-positioning').css('margin-left', '-269.5px');
								$('#widget-preview-positioning').css('margin-bottom', '-132px');
								$('#widget-preview-inner').css('border', 'none');
								$('#widget-preview-inner').css('border-radius', '3px');
								$('#widget-preview-outter').css('border-radius', '3px');
								$('#widget-preview-inner').css('background-size', 'cover');
								$('#widget-preview-inner').css('background-repeat', 'no-repeat');
								$('#widget-preview-inner').css('background-color', 'transparent');
							break;
							case 'custom':
								$('#widget-preview-inner').css('width', $('#skin_width').val() + 'px');
								$('#widget-preview-inner').css('height', $('#skin_height').val() + 'px');
								$('#widget-preview-positioning').css('width', $('#skin_width').val() + 'px');
								$('#widget-preview-positioning').css('height', $('#skin_height').val() + 'px');
								$('#widget-preview-positioning').css('margin-left', '-' + ($('#skin_width').val()/2) + 'px');
								$('#widget-preview-positioning').css('margin-bottom', '-' + ($('#skin_height').val()/2) + 'px');
								$('#widget-preview-inner').css('background-image', 'none');
								$('#widget-preview-inner').css('border', 'none');
								$('#widget-preview-inner').css('border-radius', '3px');
								$('#widget-preview-outter').css('border-radius', '3px');
								$('[data-showhide-skindesign="custom"]').find('input, select').trigger('change');
							break;
							case 'css':
								$('#widget-preview-inner').css('width', $('#skin_width').val() + 'px');
								$('#widget-preview-inner').css('height', $('#skin_height').val() + 'px');
								$('#widget-preview-positioning').css('width', $('#skin_width').val() + 'px');
								$('#widget-preview-positioning').css('height', $('#skin_height').val() + 'px');
								$('#widget-preview-positioning').css('margin-left', '-' + ($('#skin_width').val()/2) + 'px');
								$('#widget-preview-positioning').css('margin-bottom', '-' + ($('#skin_height').val()/2) + 'px');
								$('#widget-preview-inner').css('background', 'none');
								$('[data-showhide-skindesign="css"]:last').find('input, select').trigger('change');

								if ($('#skin_css_bg_type').val() !== null) {
									$('#skin_css_bg_type').trigger('change');
								}
							break;
						}
					break;
					// Desktop
					// --------------------------------------------------
					// Simple presets
					case 'skin_preset_id':
console.log('fire');
						desktop.find('#widget-preview-inner').css('background-image', 'url(//w.sharecash.org/skins/' + $('.slider-for .slick-current img').attr('src').split('\\').pop().split('/').pop()+')');
						desktop.find('#widget-preview-inner').css('background-repeat', 'no-repeat');
						desktop.find('#widget-preview-inner').css('background-size', 'cover');
					break;
					// Custom Image
					case 'skin_width':
						desktop.find('#widget-preview-positioning').css('width', $(this).val() + 'px');
						desktop.find('#widget-preview-positioning').css('margin-left', '-' + ($(this).val() / 2) + 'px');
						desktop.find('#widget-preview-inner').css('width', $(this).val() + 'px');
					break;
					case 'skin_height':
						desktop.find('#widget-preview-positioning').css('height', $(this).val() + 'px');
						desktop.find('#widget-preview-positioning').css('margin-bottom', '-' + ($(this).val() / 2) + 'px');
						desktop.find('#widget-preview-inner').css('height', $(this).val() + 'px');
					break;
					case 'skin_custom_bg':
						desktop.find('#widget-preview-inner').css('background-image', 'url(' + $(this).val() + ')');
					break;
					case 'skin_custom_bg_sizing':
						desktop.find('#widget-preview-inner').css('background-size', $(this).val());
					break;
					case 'skin_custom_bg_repeat':
						desktop.find('#widget-preview-inner').css('background-repeat', $(this).val());
					break;
					case 'skin_custom_bg_img_color':
						desktop.find('#widget-preview-inner').css('background-color', $(this).val());
					break;
					// Advanced CSS
					case 'skin_css_bg_type':
						switch ($(this).val()) {
							case 'image':
								desktop.find('#widget-preview-inner').css('background-image', 'url(' + $('#skin_css_bg_url').val() + ')');
								desktop.find('#widget-preview-inner').css('background-size', $('#skin_css_bg_sizing').val());
								desktop.find('#widget-preview-inner').css('background-repeat', $('#skin_css_bg_repeat').val());
								desktop.find('#widget-preview-inner').css('background-color', $('#skin_css_bg_img_color').val());
							break;
							case 'gradient':
								setGradient('#widget-preview-inner', $('#skin_css_bg_grad_dir').val(), $('#skin_css_bg_grad_start').val(), $('#skin_css_bg_grad_end').val());
							break;
							case 'solid':
								desktop.find('#widget-preview-inner').css('background-image', 'none');
								desktop.find('#widget-preview-inner').css('background-color', $('#skin_css_bg_color').val());
							break;
						}
					break;
					// Image background type
					case 'skin_css_bg_url':
						desktop.find('#widget-preview-inner').css('background-image', 'url(' + $(this).val() + ')');
					break;
					case 'skin_css_bg_sizing':
						desktop.find('#widget-preview-inner').css('background-size', $(this).val());
					break;
					case 'skin_css_bg_repeat':
						desktop.find('#widget-preview-inner').css('background-repeat', $(this).val());
					break;
					case 'skin_css_bg_img_color':
						desktop.find('#widget-preview-inner').css('background-color', $(this).val());
					break;
					// Gradient background type
					case 'skin_css_bg_grad_dir':
						setGradient('#widget-preview-inner', $('#skin_css_bg_grad_dir').val(), $('#skin_css_bg_grad_start').val(), $('#skin_css_bg_grad_end').val());
					break;
					case 'skin_css_bg_grad_start':
						setGradient('#widget-preview-inner', $('#skin_css_bg_grad_dir').val(), $('#skin_css_bg_grad_start').val(), $('#skin_css_bg_grad_end').val());
					break;
					case 'skin_css_bg_grad_end':
						setGradient('#widget-preview-inner', $('#skin_css_bg_grad_dir').val(), $('#skin_css_bg_grad_start').val(), $('#skin_css_bg_grad_end').val());
					break;
					// Solid color type
					case 'skin_css_bg_color':
						desktop.find('#widget-preview-inner').css('background-color', $(this).val());
					break;
					// Border
					case 'skin_css_border_type':
						desktop.find('#widget-preview-inner').css('border-style', $(this).val());
					break;
					case 'skin_css_border_color':
						desktop.find('#widget-preview-inner').css('border-color', $(this).val());
					break;
					case 'skin_css_border_size':
						desktop.find('#widget-preview-inner').css('border-width', $(this).val() + 'px');
					break;
					case 'skin_css_border_radius':
						desktop.find('#widget-preview-inner').css('border-radius', $(this).val() + 'px');
						desktop.find('#widget-preview-outter').css('border-radius', $(this).val() + 'px');
					break;
					// Design
					// --------------------------------------------------
					// Header type text
					case 'design_header_type':
						if ($(this).val() === 'none') {
							desktop.find('#widget-preview-header').css('display', 'none');
						} else {
							desktop.find('#widget-preview-header').css('display', 'block');

							var imgTag = desktop.find('#widget-preview-header').find('img');

							if ($(this).val() === 'text') {
								if (imgTag.length !== 0) {
									imgTag.remove();
								}
								$('[data-showhide-headertype="text"]').find('input, select').trigger('change');
							} else {
								if (imgTag.length === 0) {
									desktop.find('#widget-preview-header').text('');
								}
								$('[data-showhide-headertype="image"]').find('input, select').trigger('change');
							}
						}
					break;
					case 'design_header_text':
						desktop.find('#widget-preview-header').text($(this).val());
					break;
					case 'design_header_font':
						var parsedFont = parseFont($(this).val());
						desktop.find('#widget-preview-header').css('font-family', '\'' + parsedFont.font + '\', sans-serif');
						desktop.find('#widget-preview-header').css('font-weight', parsedFont.weight);
					break;
					case 'design_header_text_align':
						desktop.find('#widget-preview-header').css('text-align', $(this).val());
					break;
					case 'design_header_text_color':
						desktop.find('#widget-preview-header').css('color', $(this).val());
					break;
					case 'design_header_text_size':
						desktop.find('#widget-preview-header').css('font-size', $(this).val() + 'px');
					break;
					case 'design_header_text_mtop':
						desktop.find('#widget-preview-header').css('margin-top', $(this).val() + 'px');
					break;
					case 'design_header_text_mbot':
						desktop.find('#widget-preview-header').css('margin-bottom', $(this).val() + 'px');
					break;
					case 'design_header_text_shadow_size':
						desktop.find('#widget-preview-header').css('text-shadow', $('#design_header_text_shadow_size').val() + 'px ' + $('#design_header_text_shadow_size').val() + 'px ' + $('#design_header_text_shadow_blur').val() + 'px ' + $('#design_header_text_shadow_color').val());
					break;
					case 'design_header_text_shadow_blur':
						desktop.find('#widget-preview-header').css('text-shadow', $('#design_header_text_shadow_size').val() + 'px ' + $('#design_header_text_shadow_size').val() + 'px ' + $('#design_header_text_shadow_blur').val() + 'px ' + $('#design_header_text_shadow_color').val());
					break;
					case 'design_header_text_shadow_color':
						desktop.find('#widget-preview-header').css('text-shadow', $('#design_header_text_shadow_size').val() + 'px ' + $('#design_header_text_shadow_size').val() + 'px ' + $('#design_header_text_shadow_blur').val() + 'px ' + $('#design_header_text_shadow_color').val());
					break;
					// Header type image
					case 'design_header_image_url':
						desktop.find('#widget-preview-header').html('<img src="' + htmlentities($(this).val(), 'ENT_QUOTES') + '" border="0" alt="">');
					break;
					case 'design_header_image_align':
						desktop.find('#widget-preview-header').css('text-align', $(this).val());
					break;
					case 'design_header_image_mtop':
						desktop.find('#widget-preview-header').css('margin-top', $(this).val() + 'px');
					break;
					case 'design_header_image_mbot':
						desktop.find('#widget-preview-header').css('margin-bottom', $(this).val() + 'px');
					break;
					// Instructions
					case 'design_instruct_show':
						if ($(this).val() === '0') {
							desktop.find('#widget-preview-instruct').css('display', 'none');
						} else {
							desktop.find('#widget-preview-instruct').css('display', 'block');
							$('[data-showhide-instructions="yes"]').find('input, select').trigger('change');
						}
					break;
					case 'design_instruct_text':
						desktop.find('#widget-preview-instruct').text($(this).val());
					break;
					case 'design_instruct_font':
						var parsedFont = parseFont($(this).val());
						desktop.find('#widget-preview-instruct').css('font-family', '\'' + parsedFont.font + '\', sans-serif');
						desktop.find('#widget-preview-instruct').css('font-weight', parsedFont.weight);
					break;
					case 'design_instruct_align':
						desktop.find('#widget-preview-instruct').css('text-align', $(this).val());
					break;
					case 'design_instruct_color':
						desktop.find('#widget-preview-instruct').css('color', $(this).val());
					break;
					case 'design_instruct_size':
						desktop.find('#widget-preview-instruct').css('font-size', $(this).val() + 'px');
					break;
					case 'design_instruct_mtop':
						desktop.find('#widget-preview-instruct').css('margin-top', $(this).val() + 'px');
					break;
					case 'design_instruct_mbot':
						desktop.find('#widget-preview-instruct').css('margin-bottom', $(this).val() + 'px');
					break;
					case 'design_instruct_shadow_size':
						desktop.find('#widget-preview-instruct').css('text-shadow', $('#design_instruct_shadow_size').val() + 'px ' + $('#design_instruct_shadow_size').val() + 'px ' + $('#design_instruct_shadow_blur').val() + 'px ' + $('#design_instruct_shadow_color').val());
					break;
					case 'design_instruct_shadow_blur':
						desktop.find('#widget-preview-instruct').css('text-shadow', $('#design_instruct_shadow_size').val() + 'px ' + $('#design_instruct_shadow_size').val() + 'px ' + $('#design_instruct_shadow_blur').val() + 'px ' + $('#design_instruct_shadow_color').val());
					break;
					case 'design_instruct_shadow_color':
						desktop.find('#widget-preview-instruct').css('text-shadow', $('#design_instruct_shadow_size').val() + 'px ' + $('#design_instruct_shadow_size').val() + 'px ' + $('#design_instruct_shadow_blur').val() + 'px ' + $('#design_instruct_shadow_color').val());
					break;
					// Offers
					case 'design_offers_font':
						var parsedFont = parseFont($(this).val());
						desktop.find('#widget-preview-offers').css('font-family', '\'' + parsedFont.font + '\', sans-serif');
						desktop.find('#widget-preview-offers').css('font-weight', parsedFont.weight);
					break;
					case 'design_offers_align':
						desktop.find('#widget-preview-offers').css('text-align', $(this).val());
					break;
					case 'design_offers_color':
						desktop.find('#widget-preview-offers').css('color', $(this).val());
					break;
					case 'design_offers_size':
						desktop.find('#widget-preview-offers').css('font-size', $(this).val() + 'px');
					break;
					case 'design_offers_mtop':
						desktop.find('#widget-preview-offers').css('margin-top', $(this).val() + 'px');
					break;
					case 'design_offers_mleft':
						desktop.find('#widget-preview-offers').css('margin-left', $(this).val() + 'px');
					break;
					case 'design_offers_mright':
						desktop.find('#widget-preview-offers').css('margin-right', $(this).val() + 'px');
					break;
					case 'design_offers_shadow_size':
						desktop.find('#widget-preview-offers').css('text-shadow', $('#design_offers_shadow_size').val() + 'px ' + $('#design_offers_shadow_size').val() + 'px ' + $('#design_offers_shadow_blur').val() + 'px ' + $('#design_offers_shadow_color').val());
					break;
					case 'design_offers_shadow_blur':
						desktop.find('#widget-preview-offers').css('text-shadow', $('#design_offers_shadow_size').val() + 'px ' + $('#design_offers_shadow_size').val() + 'px ' + $('#design_offers_shadow_blur').val() + 'px ' + $('#design_offers_shadow_color').val());
					break;
					case 'design_offers_shadow_color':
						desktop.find('#widget-preview-offers').css('text-shadow', $('#design_offers_shadow_size').val() + 'px ' + $('#design_offers_shadow_size').val() + 'px ' + $('#design_offers_shadow_blur').val() + 'px ' + $('#design_offers_shadow_color').val());
					break;
					// Widget Box
					case 'design_widget_overlay':
						desktop.find('#widget-preview-overlay').css('background', $(this).val());
					break;
					case 'design_widget_shadow_size':
						desktop.find('#widget-preview-outter').css('box-shadow', '0px 0px ' + $('#design_widget_shadow_blur').val() + 'px ' + $('#design_widget_shadow_size').val() + 'px ' + $('#design_widget_shadow_color').val());
					break;
					case 'design_widget_shadow_blur':
						desktop.find('#widget-preview-outter').css('box-shadow', '0px 0px ' + $('#design_widget_shadow_blur').val() + 'px ' + $('#design_widget_shadow_size').val() + 'px ' + $('#design_widget_shadow_color').val());
					break;
					case 'design_widget_shadow_color':
						desktop.find('#widget-preview-outter').css('box-shadow', '0px 0px ' + $('#design_widget_shadow_blur').val() + 'px ' + $('#design_widget_shadow_size').val() + 'px ' + $('#design_widget_shadow_color').val());
					break;
					// Mobile
					// --------------------------------------------------
					// Background
					case 'mobile_bg_grad_dir':
						setGradient('body', $('#mobile_bg_grad_dir').val(), $('#mobile_bg_grad_start').val(), $('#mobile_bg_grad_end').val(), true);
					break;
					case 'mobile_bg_grad_start':
						setGradient('body', $('#mobile_bg_grad_dir').val(), $('#mobile_bg_grad_start').val(), $('#mobile_bg_grad_end').val(), true);
					break;
					case 'mobile_bg_grad_end':
						setGradient('body', $('#mobile_bg_grad_dir').val(), $('#mobile_bg_grad_start').val(), $('#mobile_bg_grad_end').val(), true);
					break;
					// Header
					case 'mobile_header_text':
						mobile.find('#header_text').text($(this).val());
					break;
					case 'mobile_header_font':
						var parsedFont = parseFont($(this).val());
						mobile.find('link:last').after('<link href="//fonts.googleapis.com/css?family=' + htmlentities($(this).val(), 'ENT_QUOTES') + '" rel="stylesheet" type="text/css">');
						mobile.find('#header_text').css('font-family', '\'' + parsedFont.font + '\', sans-serif');
						mobile.find('#header_text').css('font-weight', parsedFont.weight);
					break;
					case 'mobile_header_color':
						mobile.find('#header_text').css('color', $(this).val());
					break;
					case 'mobile_header_size':
						mobile.find('#header_text').css('font-size', $(this).val() + 'px');
					break;
					case 'mobile_header_shadow_size':
						mobile.find('#header_text').css('text-shadow', $('#mobile_header_shadow_size').val() + 'px ' + $('#mobile_header_shadow_size').val() + 'px ' + $('#mobile_header_shadow_blur').val() + 'px ' + $('#mobile_header_shadow_color').val());
					break;
					case 'mobile_header_shadow_blur':
						mobile.find('#header_text').css('text-shadow', $('#mobile_header_shadow_size').val() + 'px ' + $('#mobile_header_shadow_size').val() + 'px ' + $('#mobile_header_shadow_blur').val() + 'px ' + $('#mobile_header_shadow_color').val());
					break;
					case 'mobile_header_shadow_color':
						mobile.find('#header_text').css('text-shadow', $('#mobile_header_shadow_size').val() + 'px ' + $('#mobile_header_shadow_size').val() + 'px ' + $('#mobile_header_shadow_blur').val() + 'px ' + $('#mobile_header_shadow_color').val());
					break;
					case 'mobile_header_grad_dir':
						setGradient('#header_text', $('#mobile_header_grad_dir').val(), $('#mobile_header_grad_start').val(), $('#mobile_header_grad_end').val(), true);
					break;
					case 'mobile_header_grad_start':
						setGradient('#header_text', $('#mobile_header_grad_dir').val(), $('#mobile_header_grad_start').val(), $('#mobile_header_grad_end').val(), true);
					break;
					case 'mobile_header_grad_end':
						setGradient('#header_text', $('#mobile_header_grad_dir').val(), $('#mobile_header_grad_start').val(), $('#mobile_header_grad_end').val(), true);
					break;
					// Instructions
					case 'mobile_instruct_font':
						var parsedFont = parseFont($(this).val());
						mobile.find('link:last').after('<link href="//fonts.googleapis.com/css?family=' + htmlentities($(this).val(), 'ENT_QUOTES') + '" rel="stylesheet" type="text/css">');
						mobile.find('#instructions_text').css('font-family', '\'' + parsedFont.font + '\', sans-serif');
						mobile.find('#instructions_text').css('font-weight', parsedFont.weight);
					break;
					case 'mobile_instruct_color':
						mobile.find('#instructions_text').css('color', $(this).val());
					break;
					case 'mobile_instruct_size':
						mobile.find('#instructions_text').css('font-size', $(this).val() + 'px');
					break;
					case 'mobile_instruct_bg_color':
						mobile.find('#wrap').css('background-color', $(this).val());
					break;
					case 'mobile_instructions_text':
						if ($(this).val().length === 0) {
							mobile.find('#instructions_text').fadeOut();
						} else {
							mobile.find('#instructions_text').fadeIn();
						}
						mobile.find('#instructions_text').text($(this).val());
					break;
					case 'mobile_instruct_waiting_text':
						mobile.find('#after_click > h2').text($(this).val());
					break;
					case 'mobile_instructions_retry':
						mobile.find('#after_click > ul > li').text($(this).val());
					break;
					// Offers
					case 'mobile_offers_font':
						var parsedFont = parseFont($(this).val());
						mobile.find('link:last').after('<link href="//fonts.googleapis.com/css?family=' + htmlentities($(this).val(), 'ENT_QUOTES') + '" rel="stylesheet" type="text/css">');
						mobile.find('ul > li').css('font-family', '\'' + parsedFont.font + '\', sans-serif');
						mobile.find('ul > li').css('font-weight', parsedFont.weight);
					break;
					case 'mobile_offers_color':
						mobile.find('ul > li').css('color', $(this).val());
					break;
					case 'mobile_offers_size':
						mobile.find('ul > li').css('font-size', $(this).val() + 'px');
					break;
					case 'mobile_offers_bg_color':
						mobile.find('ul > li').css('background-color', $(this).val());
					break;
					case 'mobile_offers_icon_type':
						var offerIcons = mobile.find('.icon');
						offerIcons.attr('class', 'icon');
						offerIcons.css('display', 'block');
						mobile.find('ul > li').css('text-align', 'left');

						switch($(this).val()) {
							case 'none':
								offerIcons.css('display', 'none');
							break;
							case 'nonecenter':
								offerIcons.css('display', 'none');
								mobile.find('ul > li').css('text-align', 'center');
							break;
							case 'arrow':
								offerIcons.addClass('icon-arrow');
							break;
							case 'unlock':
								offerIcons.addClass('icon-unlock-tick');
							break;
						}
					break;
					case 'mobile_offers_icon_color':
						mobile.find('.icon').css('color', $(this).val());
					break;
					case 'mobile_offers_icon_bg':
						mobile.find('.icon').css('background-color', $(this).val());
					break;
				}
			};

			$(document).on('change', 'form#widget-create-form select', updatePreview);
			$(document).on('change', 'form#widget-create-form input', updatePreview);
			$(document).on('keyup', 'form#widget-create-form input', updatePreview);

			if (typeof EDIT_WIDGET !== 'undefined' && EDIT_WIDGET !== false) {
				var editValues = $.parseJSON(EDIT_WIDGET);

				$.each(editValues, function(i, v){
					var elm = $('#' + i);

					if (elm.length) {
						elm.val(v).trigger('change');

						if (i === 'skin_preset_id') {
							console.log('preset - ' + v);
							skinSelect.slick('slickGoTo', parseInt(v) - 1);
							$('.slider-nav .slick-current').click();
						}
					}
				});

				EDIT_WIDGET = false;
			}

		break; //}
		case 'tools_widget_manage': //{

			var clonedWidgetId = null;
			$(document).on('click', '.widget-manage-clone', function(e){
				clonedWidgetId = $(this).closest("tr").data("widgetid");
				console.log("the id is " + clonedWidgetId);
				modalOpen($("#widgetCloneModal"));
			});

			$("#clone-widget").click(function (event) {
				var clonedName = $("#cloned-widget-name").val();

				var panel = $("#widgetCloneModal div.panel-body");
				panel.addClass("loading-mask");

				$.get('data.php', {
					cmd: 'widgets_clone',
					id: clonedWidgetId,
					name: clonedName,
				}, function (data) {
					panel.removeClass("loading-mask");
					hideAlerts('main-body');

					if (data.status == 'error') {
                        innerMainMessage('main-body', 'error', data.data);
					} else {
						var clonedId = data.data;
						var clonedRow = $("tr[data-widgetid='" + clonedWidgetId + "']").clone();

						$(clonedRow).data('widgetid', clonedId);
						$(clonedRow).attr('data-widgetid', clonedId);
						$(clonedRow).find("td:eq(0)").html(clonedId);
						$(clonedRow).find("td:eq(1)").html(clonedName);
						dt._fnAddTr($(clonedRow)[0]);
						dt._fnReDraw();

                        innerMainMessage('main-body', 'success', "The new widget has been created.");
					}

					$("#cloned-widget-name").val("");
					clonedWidgetId = null;
				}, 'json');
			});

			$(document).on('click', '.widget-manage-code', function(e){
				var tr = $(this).parent().parent();
				var wid = tr.data('widgetid');
				var randDomain = randWidgetDomain();

				$('#widget-manage-instant-code').val('<script src="https://' + randDomain + '/' + wid + '.js"></script><script>\'undefined\'==typeof ld&&(window.location=\'https://' + randDomain + '/error/block\');</script><noscript><meta http-equiv="refresh" content="0;url=https://' + randDomain + '/error/nojs"/></noscript>');
				$('#widget-manage-event-code').val('<script src="https://' + randDomain + '/event.js"></script><script>\'undefined\'==typeof ld&&(window.location=\'https://' + randDomain + '/error/block\');</script><noscript><meta http-equiv="refresh" content="0;url=https://' + randDomain + '/error/nojs"/></noscript>');
				$('#widget-manage-event-func').val('start_widget(' + wid + ');');
				$('.widgets-manage-wid').text(wid);
				modalOpen($('#widgetManageCode'), false);
			});

			$('#widgets-manage').on('draw.dt', function() {
				$('.widget-manage-del').confirmation({
					placement: 'top',
					onConfirm: function(event, el) {
	                    var widgetId = $(el).closest("tr").data('widgetid');
				        var panel = $('#widgets-manage');
				        panel.addClass('loading-mask');

	                    $.get('data.php',
	                    {
	                        cmd: 'widgets_delete',
	                        id: widgetId
	                    }, function (data) {
	                    	panel.removeClass('loading-mask');

	                        if (data.status == 'error')
	                        {
	                            innerMainMessage('main-body', 'error', data.data);
	                        }
	                        else if (data.status == 'ok')
	                        {
	                            var row = $("tr[data-widgetid=" + widgetId + "]");
	                            dt.fnDeleteRow(row, null, true);
	                            dt.fnDraw(false);
	                        }
	                    }, 'json');
					}
				});
			});
			var dt = $('#widgets-manage').dataTable();
			window.dt = dt;

		break; //}
        case 'tools_linklocker_manage': //{
			$('#linklockers-manage').on('draw.dt', function() {
	            $('.linklocker-manage-del').confirmation({
	                placement: 'top',
	                onConfirm: function(event, el) {
	                    var lockerId = $(el).closest("tr").data('linklocker-id');
				        var panel = $('section.ll-list');
				        panel.addClass('loading-mask');

	                    $.get('data.php',
	                    {
	                        cmd: 'linklocker_delete',
	                        id: lockerId
	                    }, function (data) {
	                    	panel.removeClass('loading-mask');

	                        if (data.status == 'error')
	                        {
	                            innerMainMessage('main-body', 'error', data.data);
	                        }
	                        else if (data.status == 'ok')
	                        {
	                            var row = $("tr[data-linklocker-id=" + lockerId + "]");
	                            dt.fnDeleteRow(row, null, true);
	                            dt.fnDraw(false);
	                        }
	                    }, 'json');
	                }
	            });
	        });
			var dt = $('#linklockers-manage').dataTable();

            $("a.get-link").click(function () {
                var lockerId = $(this).closest("tr").data('linklocker-id');
                $('#link_locker_url').val('http://fileml.com/l/0' + maskUrl(lockerId));
                modalOpen("#linkLockerModal");
            });

        break; //}
		case 'library_game_guides': //{
			$('.game_guide_add').on('click',function()
			{
				hideAlerts('msg_div');
				var panel = $(this).closest('.inner-wrapper');
				panel.addClass('loading-mask');

				$.post('data.php?cmd=game_guide_add',
					{
						'game_guide_id' : $(this).attr('data-game-guide-id')
					},
					function(data)
					{
						panel.removeClass('loading-mask');

						if (data.status == 'error')
						{
							innerMainMessage('msg_div', 'error', data.data);
						}
						else if (data.status == 'ok')
						{
							innerMainMessage('msg_div', 'success', 'The game guide was added to your account. You can find it in your File Manager.');
						}
					},
					'json'
				);

				return false;
			});

			$('.game_guide_download').on('click',function()
			{
				hideAlerts('main');
				var panel = $(this).closest('.inner-wrapper');
				panel.addClass('loading-mask');

				$.post('data.php?cmd=game_guide_download',
					{
						'game_guide_id' : $(this).attr('data-game-guide-id')
					},
					function(data)
					{
						panel.removeClass('loading-mask');

						if (data.status == 'error')
						{
							innerMainMessage('msg_div', 'error', data.data);
						}
						else if (data.status == 'ok')
						{
							window.location = data.link;
						}
					},
					'json'
				);

				return false;
			});
		break; //}

        case 'tools_linklocker_create': //{
        case 'tools_linklocker_edit':
            /*$('#widget-create').find('ul.pager li.finish').on('click', function(e) {
                e.preventDefault();

                $('.form-wizard .tab-pane.active').find('select, input, textarea').each(validateHandle);
                var check = $('.has-error');
                if (check.length) {
                    check.focus();
                    return false;
                }

                var data = {};
                $.each($('form#widget-create-form').find('input,select,textarea'), function(i,v){
                    var input = $(v);
                    data[input.attr('id')] = input.val();
                });

                data['version'] = 3;
                console.log(data);

                $.post('data.php?cmd=tools_widget_create', data, function(data) {
                    if (data.status == 'error')
                    {
                        innerMainMessage('tools_widget_create_alerts', 'error', data.data);
                    }
                    else if (data.status == 'ok')
                    {

                    }
                }, 'json');
            });*/

            $('#widget-create').bootstrapWizard({
                tabClass: 'wizard-steps',
                nextSelector: 'ul.pager li.next',
                previousSelector: 'ul.pager li.previous',
                firstSelector: null,
                lastSelector: null,
                onNext: function( tab, navigation, index, newindex ) {
                    $('.form-wizard .tab-pane.active').find('select, input, textarea').each(validateHandle);
                    var check = $('.has-error');
                    if (check.length) {
                        check.focus();
                        return false;
                    }
                },
                onTabClick: function( tab, navigation, index, newindex ) {
                    if ( newindex == index + 1 ) {
                        return this.onNext( tab, navigation, index, newindex);
                    } else if ( newindex > index + 1 ) {
                        return false;
                    } else {
                        return true;
                    }
                },
                onTabChange: function( tab, navigation, index, newindex ) {
                    var $total = navigation.find('li').size() - 1;
                    $('#widget-create').find('ul.pager li.finish')[ newindex != $total ? 'addClass' : 'removeClass' ]( 'hidden' );
                    $('#widget-create').find(this.nextSelector)[ newindex == $total ? 'addClass' : 'removeClass' ]( 'hidden' );
                },
                onTabShow: function( tab, navigation, index ) {
                    var $total = navigation.find('li').length - 1;
                    var $current = index;
                    var $percent = Math.floor(( $current / $total ) * 100);
                    $('#widget-create').find('.progress-indicator').css({ 'width': $percent + '%' });
                    tab.prevAll().addClass('completed');
                    tab.nextAll().removeClass('completed');

                    if (tab.html().indexOf('mobile') !== -1) {
                        $('a[href="#widget-create-preview-mobile"]').click();
                    }
                }
            });

            /*if (typeof EDIT_WIDGET !== 'undefined' && EDIT_WIDGET !== false) {
                var editValues = $.parseJSON(EDIT_WIDGET);

                $.each(editValues, function(i, v){
                    var elm = $('#' + i);

                    if (elm.length) {
                        elm.val(v).trigger('change');
                    }
                });

                EDIT_WIDGET = false;
            }*/

        break; //}
	}
};
$(document).ready(onLoad);
