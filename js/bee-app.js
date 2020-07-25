/**
 * EmakBEE js
 *
 * @author hgodinho
 */
//import { toPng } from 'html-to-image';

var token = hgodbee_object.token;
console.log(token);
var ajaxURL = hgodbee_object.ajax_url;
//var plugindir = emakbee_infos.plugin_dir;

function getBeeConfig(user_or_account_id, container_id) {
	return {
		uid: '__' + user_or_account_id,
		container: container_id,
		autosave: 30,
		preventClose: false,
		language: 'pt-BR',
	};
}

function BeeApp(token, bee_config, beetemplates) {
	// @god mudar client_id e client_secret para token
	console.log(beetemplates);

	BeeApp.singleton = this;
	BeeApp.singleton.callbacks = {};
	var that = this;
	this.bee_config = bee_config;

	/* Use the leftside as a staging area to make transitions smoother.*/
	function offscreenElement(element) {
		if (element) {
			element.oldStyle = {
				position: element.style.position,
				left: element.style.left,
			};
			element.style.position = 'absolute';
			element.style.left = '-999999px';
		}
	}

	function onscreenElement(element) {
		if (element && element.style) {
			element.style.position = element.oldStyle.position;
			element.style.left = element.oldStyle.left;
		}
	}

	/* An HTMLProcessor instance is used to post-process HTML being returned
       from the BEE editor onSave(), etc.
    */
	function HTMLProcessor() {
		/**************************************************************************
          4.1.                                                             THUMBNAILS
          ***************************************************************************
      
          Quite often you'll want to generate thumbnails of the generated HTML. This 
          is one method to do so in the client browser.
      
          *** TUTORIAL NOTE:
          Because of browser security, this will not render cross-origin images. If 
          you want to use html2canvas, you will need to implement a proxy.
      
          --> SEE ALSO https://html2canvas.hertzen.com/documentation.html
          --> SEE ALSO https://github.com/niklasvh/html2canvas/wiki/Proxies
      
          It's recommended you take a screenshot on the server, using PhantomJS. 
          e.g. phantom rasterize.js http://www.google.com google.png
      
          --> SEE ALSO http://phantomjs.org/screen-capture.html
          */
		function html2png(html, callback) {
			var doc = document;
			var iframe = document.createElement('iframe');
			iframe.style.width = '1024px';
			offscreenElement(iframe);
			doc.body.appendChild(iframe);

			iframe.contentWindow.document.body.innerHTML = html;
			/*
      html2canvas(iframe.contentWindow.document.body, {
        onrendered: function (canvas) {
          callback(canvas.toDataURL("image/png"));
          doc.body.removeChild(iframe);
        },
      });
      */
			domtoimage
				.toPng(html)
				.then(function (dataUrl) {
					var img = new Image();
					img.src = dataUrl;
					document.body.appendChild(img);
				})
				.catch(function (error) {
					console.error('oops, something went wrong!', error);
				});
		}

		// The main method called from onSave(), etc.
		this.process = function (html, callback) {
			/*html2png(html, function (png) {
				callback(html, png);
			});*/
		};
	}

	function BeeCallbacks(app, bee_config) {
		this.app = app;
		bee_config.onLoad = function () {
			app.onLoadEditor();
		};

		(bee_config.onAutoSave = function (jsonFile) {
			//window.localStorage.setItem("autosave.json", jsonFile);
			var data = {
				action: 'emakbee_autosave',
				json: jsonFile,
				nonce: emakbee_infos.nonce_autosave,
			};
			jQuery.post(ajaxURL, data, function (response) {
				//alert(response);
				console.log(response);
			});
		}),

		// VAI MUDAR PARA onExport = function(htmlFile) {}
		(bee_config.onSave = function (jsonFile, htmlFile) { 
			window.localStorage.setItem('save.json', jsonFile);
			var zip = new JSZip();
			var currentdate = new Date();
			var archivename =
				'news-' +
				currentdate.getDate() +
				'-' +
				(currentdate.getMonth() + 1) +
				'-' +
				currentdate.getFullYear() +
				'@' +
				currentdate.getHours() +
				'h' +
				currentdate.getMinutes() +
				'm' +
				currentdate.getSeconds() +
				's';
			zip.file(archivename + '.html', htmlFile);
			zip.generateAsync({
				type: 'blob',
			}).then(function (content) {
				saveAs(content, archivename + '.zip');
			});
		}),

		(bee_config.onSaveAsTemplate = function (jsonFile) {
			jQuery('#templateSave').modal('show');
			jQuery('#salvarTemplateBTN').click(function () {
				var templateName = document.getElementById('nomeTemplate')
					.value;
				var templateDescription = document.getElementById(
					'descricaoTemplate'
				).value;

				if (templateName == '' || templateDescription == '') {
					alert('Os campos precisam ser preenchidos.');
					return false;
				} else {
					var data = {
						action: 'hgodbee_save_template',
						name: templateName,
						dsc: templateDescription,
						json: jsonFile,
						nonce: hgodbee_object.nonce_save_as_template,
					};
					jQuery
						.post(ajaxURL, data, function (response) {
							//alert(response);
							var notificationArea = document.getElementById(
								'notification-area'
							);
							notificationArea.innerHTML = response;
						})
						.done(jQuery('#templateSave').modal('hide'));
				}
			});
		}),

		(bee_config.onSend = function (htmlFile) {
			jQuery('#enviaTeste').modal('show');
			jQuery('#enviaTesteBTN').click(function () {
				var contatos = document.getElementById('enderecoEnvio')
					.value;
				var assunto = document.getElementById('assuntoEnvio').value;
				//var html = JSON.stringify("{'num':" + htmlFile + "}");

				if (contatos == '' || assunto == '') {
					alert('Os campos precisam ser preenchidos.');
					return false;
				} else {
					var data = {
						action: 'emakbee_envia_teste',
						contatos: contatos,
						assunto: assunto,
						html: htmlFile,
						nonce: emakbee_infos.nonce_enviateste,
					};
					jQuery
						.post(ajaxURL, data, function (response) {
							//alert(response);
							var notificationArea = document.getElementById(
								'notificationArea'
							);
							notificationArea.innerHTML = response;
						})
						.done(jQuery('#enviaTeste').modal('hide'));
				}
			});
		}),

		(bee_config.onError = function (errorMessage) {
			app.onError(errorMessage);
		});

	}

	/**************************************************************************
      4.4.                                                                SPINNER
      ***************************************************************************
      */
	function Spinner() {
		this.start = function () {
			var div = document.createElement('div');
			div.style.position = 'absolute';
			div.style.top = '0';
			div.style.left = '0';
			div.style.width = '100%';
			div.style.height = '100%';
			div.innerHTML =
				"<table style='width:100%;height:100%;''><tr><td style='width:100%;height:100%;text-align:center;vertical-align:middle'><img src='http://cdnjs.cloudflare.com/ajax/libs/galleriffic/2.0.1/css/loader.gif'/></td></tr></table>";
			document.body.appendChild(div);
			this.container = div;
			this.hide();
		};
		this.show = function () {
			onscreenElement(this.container);
		};
		this.hide = function () {
			offscreenElement(this.container);
		};
	}

	/**************************************************************************
      4.6.                                                                TOOLBAR
      ***************************************************************************
    
      You can turn off the Bee built in toolbar and implement your own in your
      application user interface.
    
      --> SEE ALSO http://help.beefree.io/hc/en-us/articles/204783822-Customizing-the-toolbar
      */
	function BeeToolbar() {
		this.start = function (pluginContainer) {
			var div = document.createElement('div');
			var result = [
				"<nav class='navbar navbar-light'><div class='container'>",
			];
			result.push(
				"<a class='nav-link' role='button' href='javascript:void(0)' onclick='BeeApp.singleton.toggleGallery()'>Templates</a>"
			);

			var buttons = [
				['Preview', 'preview'],
				['Enviar Teste', 'send'],
				['Salvar', 'saveAsTemplate'],
				['Exportar', 'save'],
				['Exibir Estrutura', 'toggleStructure'],
			];

			for (var i = 0; i < buttons.length; i++) {
				var label = buttons[i][0];
				var method = buttons[i][1];
				result = result.concat([
					"<a class='nav-link' role='button' href='javascript:void(0)' onclick='BeeApp.singleton.editor.plugin." +
					method +
					"()'>" +
					label +
					'</a>',
				]);
			}
			result.push('</div></div>');
			div.innerHTML = result.join('\n');
			pluginContainer.parentNode.insertBefore(div, pluginContainer);
			pluginContainer.style.height =
				'calc(100% - ' + jQuery(pluginContainer).offset().top + 'px)';
			this.container = div;
			this.hide();
		};
		this.show = function () {
			this.container.style.display = 'block';
		};
		this.hide = function () {
			this.container.style.display = 'none';
		};
	}


	function BeeEditor() {
		var that = this;
		this.plugin = undefined;

		this.start = function (token, bee_config) {
			this.container = document.getElementById(bee_config.container);
			this.container.style.position = 'absolute';
			this.container.style.width = '100%';
			this.container.style.height = '100%';
			offscreenElement(this.container);

			BeePlugin.create(hgodbee_object.token, bee_config, function (bee) {
				bee.start();
				that.plugin = bee;
			});
		};

		this.useTemplate = function (template) {
			this.template = template;
		};

		this.show = function () {
			if (!this.plugin) {
				setTimeout(function () {
					that.show();
					//console.log("!");
				}, 10);

				return;
			}

			this.plugin.start(this.template);
			console.log(this.template);

			onscreenElement(that.container);
		};

		this.hide = function () {
			offscreenElement(this.container);
		};
		return this;
	}


	function BeeSender() {
		this.thumbnail = undefined;
		this.start = function () {
			var iframe = document.createElement('iframe');
			iframe.style.position = 'absolute';
			iframe.style.top = '0';
			iframe.style.left = '0';
			iframe.style.width = '100%';
			iframe.style.height = '100%';
			document.body.appendChild(iframe);
			this.container = iframe;
			this.hide();
		};
		this.set_html = function (html) {
			this.html = html;
		};
		this.set_thumbnail = function (src) {
			this.thumbnail = src;
		};
		this.show = function () {
			this.container.contentWindow.document.body.innerHTML = this.html;
			onscreenElement(this.container);
		};
		this.hide = function () {
			offscreenElement(this.container);
		};
	}


	this.html_processor = new HTMLProcessor();
	this.callbacks = new BeeCallbacks(this, bee_config);
	this.spinner = new Spinner();
	//this.toolbar = new BeeToolbar(this.pluginContainer);
	this.editor = new BeeEditor();
	this.sender = new BeeSender();

	// Start the application by preloading the plugin, spinner, template gallery
	// toolbar, content dialog, email send stub.
	//
	this.start = function () {
		this.pluginContainer = document.getElementById(bee_config.container);
		this.spinner.start();
		this.editor.start(token, bee_config); // @god mudanca para token
		this.editor.useTemplate(beetemplates);
		//this.toolbar.start(this.pluginContainer);
		this.sender.start(); // @@god comentado por teste
		this.editor.show(); // @god atenção aqui
	};


	// Smoothly display the editor.
	//
	// Called from onLoad to make a smooth transition once and only
	// once the plugin is loaded and ready.
	this.onLoadEditor = function () {
		//this.toolbar.show();
		this.spinner.hide();
	};

	// Called once the user wants to send the email.
	//
	// In your application, the HTML will require post-processing if you're
	// using any marketing automation features. Also, you'll of course need
	// to get the subject line, to:, cc:, bcc: as well. Then use whatever
	// service like Sendgrid, Mandrill, Amazon SES to send the email.
	this.onSend = function (html, thumbnail) {
		this.editor.hide();
		this.sender.set_html(html);
		this.sender.set_thumbnail(thumbnail);
		this.sender.show();
	};

	// If there are any errors, you should log them and also
	// notify the user in some way.
	this.onError = function (errorMessage) {
		console.log(errorMessage);
		alert(errorMessage);
	};

	return this;
}

// Callback map.
//
// This creates a facility to create callbacks into the
// tutorial BEE application from the BEE configuration,
// which is defined and created before the BEE App is created.
BeeApp.callback = function (name) {
	return function () {
		BeeApp.singleton.callbacks[name].apply(null, arguments);
	};
};