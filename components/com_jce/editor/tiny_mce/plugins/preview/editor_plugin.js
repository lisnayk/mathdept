/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	var each = tinymce.each;
	tinymce.create('tinymce.plugins.Preview', {
		init : function(ed, url) {
			var self = this, DOM = tinymce.DOM;

			this.editor = ed, this.url = url;

			this.active = [];

			ed.addCommand('mcePreview', function() {
				self._togglePreview();
			});
			
			ed.onBeforeExecCommand.add(function(ed, cmd, ui, v, o) {
				if (cmd == 'mcePrint') {
					if (self.getState()) {
						o.terminate = true;
						var preview = DOM.get(ed.id + '_preview_iframe');
						
						if (preview) {
							preview.contentWindow.print();
						}
					}
				}
			});
			
			ed.onInit.add(function() {
            	var s = ed.getParam('preview_state', false);
            		
            	if (typeof s != 'undefined') {
            		self.setState(!s);
            		self._togglePreview();
            	}    
            	
            	if (ed.plugins.fullscreen) {
					ed.onFullScreen.add(function(state, settings) {
		            	if (!state) {
		            		self.setState(!settings.preview_state);
		            		self._togglePreview();
		            	}
		            });
				}      		
            });

			ed.onSetContent.add(function(ed, o) {
            	if (self.getState()) {
                    self._disable();                 
                }
            });
			
			ed.onExecCommand.add(function(ed, cmd) {
				switch (cmd) {
					case 'mcePreview' :
						window.setTimeout(function() {
							self._disable();
						}, 0);
						break;
					case 'mceFullScreen' :
						break;
				}
			});
			
			ed.onNodeChange.add( function(ed, cm, n) {
                var s = self.getState();
                
                if (s) {
                	self._disable();
                }
            });

			ed.addButton('preview', {title : 'preview.preview_desc', cmd : 'mcePreview'});
			
			// add theme resize
            ed.theme.onResize.add(function() {
            	if (self.getState()) {
            		self.resize();
            	}
            });
		},
		
		getState : function() {
			return this.editor.getParam('preview_state',false);
		},
		
		setState : function(s) {
			this.editor.settings.preview_state = s;
		},
		
		getTop : function() {
            var ed = this.editor, container = ed.getContentAreaContainer();
            return container.offsetTop + Math.round((container.offsetHeight - container.clientHeight) / 2);
        },
		
		resize : function(w, h) {
			var self = this, ed = this.editor, DOM = tinymce.DOM, ifr = DOM.get(ed.id + '_ifr');
			
			w = parseFloat(w) || parseFloat(DOM.getStyle(ifr, 'width'));
			h = parseFloat(h) || parseFloat(DOM.getStyle(ifr, 'height'));

			DOM.setStyles(DOM.get(ed.id + '_preview_iframe'), {
				'width' 	: w,
				'height' 	: h
			});
		},
		
		_disable : function() {
        	var self = this;
        	window.setTimeout( function() {
				self._toggleDisabled();
            }, 0);
        },
		
		/**
         * Disables all buttons except Preview
         */
        _toggleDisabled : function() {
            var self = this, ed = this.editor, DOM = tinymce.DOM, cm = ed.controlManager;

            var state 	= this.getState();
            // store active buttons
            var active 	= DOM.select('.mceButtonActive', DOM.get(ed.id + '_toolbargroup'));

            each (active, function(n) {
                cm.setActive(n.id, !state);
            });

            each(DOM.select('.mceButton, .mceListBox, .mceSplitButton', DOM.get(ed.id + '_toolbargroup')), function(n) {            	
            	var id = n.id;
            	
            	// get splitButton id from parent
            	if (n.className.indexOf('mceSplitButton') !== -1) {
            		id = n.parentNode.id;
            	}
            	
            	if (id) {
            		cm.setDisabled(id, state);
            	}
            });

            cm.setActive('preview', state);
            cm.setActive('fullscreen', (ed.id == 'mce_fullscreen'));

            cm.setDisabled('preview', false);
            cm.setDisabled('print', false);
            cm.setDisabled('fullscreen', false);
        },
		
		_togglePreview : function(state) {
			var self = this, ed = this.editor, DOM = tinymce.DOM;
			
			var state 		= this.getState();

			var iframe 		= DOM.get(ed.id + '_ifr');
			var preview 	= DOM.get(ed.id + '_preview_iframe');
			
			var container 	= DOM.get(ed.id + '_preview_container');
			var toolbar 	= DOM.get(ed.id + '_toolbargroup');

			var w = parseFloat(iframe.clientWidth);
			var h = parseFloat(iframe.clientHeight);
			
			// Path
            var editorpath 	= DOM.get(ed.id + '_path_row');
            // Word Count
            var wordcount 	= DOM.get(ed.id + '-word-count');
            
            if (tinymce.isIE) {
            	DOM.setStyle(iframe.parentNode, 'position', 'relative');
            }

			if (!state) {
				ed.setProgressState(true);
				
				if (!container) {
					container = DOM.create('div', {
						id : ed.id + '_preview_container',
						role : 'application',
						style : {
							position : 'absolute',
							top		 : tinymce.isIE ? 0 : this.getTop()
						}
					});
					
					var parent = iframe.parentNode;
					parent.insertBefore(container, iframe);
				}
				
				if (!preview) {
					// create iframe
					preview = DOM.add(container, 'iframe', {
		                frameborder	: 0,
		                src			: 'javascript:""',
		                id			: ed.id + '_preview_iframe'
		            });

		            var html = '<html><head xmlns="http://www.w3.org/1999/xhtml">';
		            html += '<base href="' + tinymce.settings['document_base_url'] + '" />';
		            html += '<meta http-equiv="X-UA-Compatible" content="IE=7" /><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';

		            // get css
		            var css = tinymce.explode(ed.getParam('content_css'));
					
					// insert css
		            html += '<link href="' + self.url + '/css/preview.css" rel="stylesheet" type="text/css" />';
		            
		            tinymce.each(css, function(url) {
		            	html += '<link href="' + url + '" rel="stylesheet" type="text/css" />';
		            });

		            html += '</head><body style="margin:5px;">';
		            html += '</body></html>';
		            
		            var doc = preview.contentWindow.document;

		            doc.open();
		            doc.write(html);
		            doc.close();
				}
				
				DOM.setStyles(preview, {
					width : w,
					height : h
				});
				
				// hide Path                
                if (editorpath) {
                	DOM.hide(editorpath);
                } 
                // hide word count                
                if (wordcount) {
                	DOM.hide(wordcount.parentNode);
                }
				
				self._loadData(preview);
				
				DOM.setStyle(iframe, 'hidden');
				DOM.setAttrib(iframe, 'aria-hidden', true);
				
				DOM.show(container);
				container.removeAttribute('aria-hidden');
				
			} else {
				if (preview) {
					// show Path                
	                if (editorpath) {
	                	DOM.show(editorpath);
	                } 
	                // show word count                
	                if (wordcount) {
	                	DOM.show(wordcount.parentNode);
	                }
					
					var doc = preview.contentWindow.document;
					doc.body.innerHTML = '';
					
					DOM.removeClass(iframe, 'hidden');
	            	iframe.removeAttribute('aria-hidden');
	            	
					DOM.hide(container);
					DOM.setAttrib(container, 'aria-hidden', true);	
				}
			}

			this.setState(!state);
		},
		
		_loadData : function(n) {
			var self = this, ed = this.editor, s = tinymce.settings, doc = n.contentWindow.document;
			
			var query = '', args = {'format' : 'raw'};
			
			// set token
			args[ed.settings.token] = 1;
			
			tinymce.extend(args, {
				'data' : ed.getContent()
			});
			
			// create query
			for (k in args) {
				query += '&' + k + '=' + encodeURIComponent(args[k]);
			}
			
			// load preview data
			tinymce.util.XHR.send({
				url 	: s['site_url'] + 'index.php?option=com_jce&view=editor&layout=plugin&plugin=preview&component_id=' + s['component_id'],
				data 	: 'json=' + tinymce.util.JSON.serialize({'fn' : 'showPreview'}) + '&' + query,
				content_type : 'application/x-www-form-urlencoded',
				success : function(x) {
					// Logic borrowed from http://json.org - https://github.com/douglascrockford/JSON-js
	            	if (/^[\],:{}\s]*$/
	                    .test(x.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@')
	                    .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
	                    .replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
	                	 o = tinymce.util.JSON.parse(x);
	                } else {
	                	o = {
	                        error : 'Invalid JSON: ' + x
	                    };
	                }
				
					if (o.error) {
						var msg = o.error;
						
						if (o.text) {
							msg = o.text.join('');
						}
						
						ed.windowManager.alert(msg);
						ed.setProgressState(false);
						return false;
					}
	
					r = o.result;

					doc.body.innerHTML = r;
					ed.setProgressState(false);
				},
				error 	: function(e, x) {
					doc.body.innerHTML = ed.getContent();			
					ed.setProgressState(false);
				}
			});
		},

		getInfo : function() {
			return {
				longname : 'Preview',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/preview',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('preview', tinymce.plugins.Preview);
})();