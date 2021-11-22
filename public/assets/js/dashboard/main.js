$(document).ready(function () {

	// enable fileupload plugin
	$('input.profile_image').fileuploader({
		limit: 2,
		extensions: ['jpg', 'JPG', 'jpeg', 'JPEG'],
		fileMaxSize: 5,
		changeInput: ' ',
		theme: 'avatar',
		addMore: true,
		enableApi: true,
		thumbnails: {
			box: '<div class="fileuploader-wrapper">' +
				'<div class="fileuploader-items"></div>' +
				'<div class="fileuploader-droparea" data-action="fileuploader-input"><i class="fileuploader-icon-main"></i></div>' +
				'</div>' +
				'<div class="fileuploader-menu">' +
				'<button type="button" class="fileuploader-menu-open"><i class="fileuploader-icon-menu"></i></button>' +
				'<ul>' +
				'<li><a data-action="fileuploader-input"><i class="fileuploader-icon-upload"></i> ${captions.upload}</a></li>' +
				'<li><a data-action="fileuploader-edit"><i class="fileuploader-icon-edit"></i> ${captions.edit}</a></li>' +
				'</ul>' +
				'</div>',
			item: '<div class="fileuploader-item">' +
				'${image}' +
				'<span class="fileuploader-action-popup" data-action="fileuploader-edit"></span>' +
				'<div class="progressbar3" style="display: none"></div>' +
				'</div>',
			item2: null,
			itemPrepend: true,
			startImageRenderer: true,
			canvasImage: true,
			_selectors: {
				list: '.fileuploader-items'
			},
			popup: {
				arrows: false,
				onShow: function (item) {
					item.popup.html.addClass('is-for-avatar');
					item.popup.html.on('click', '[data-action="remove"]', function (e) {
						item.popup.close();
						item.remove();
					}).on('click', '[data-action="cancel"]', function (e) {
						item.popup.close();
					}).on('click', '[data-action="save"]', function (e) {
						if (item.editor && !item.isSaving) {
							item.isSaving = true;
							item.editor.save();
						}
						if (item.popup.close)
							item.popup.close();
					});
				},
				onHide: function (item) {
					if (!item.isSaving && !item.uploaded && !item.appended) {
						item.popup.close = null;
						item.remove();
					}
				}
			},
			onItemShow: function (item) {
				if (item.choosed)
					item.html.addClass('is-image-waiting');
			},
			onImageLoaded: function (item, listEl, parentEl, newInputEl, inputEl) {
				if (item.choosed && !item.isSaving) {
					if (item.reader.node && item.reader.width >= 256 && item.reader.height >= 256) {
						item.image.hide();
						item.popup.open();
						item.editor.cropper();
					} else {
						item.remove();
						alert('The image is too small!');
					}
				} else if (item.data.isDefault)
					item.html.addClass('is-default');
				else if (item.image.hasClass('fileuploader-no-thumbnail'))
					item.html.hide();
			},
			onItemRemove: function (html) {
				html.fadeOut(250, function () {
					html.remove();
				});
			}
		},
		dragDrop: {
			container: '.fileuploader-wrapper'
		},
		editor: {
			maxWidth: 500,
			maxHeiht: 500,
			quality: 100,
			cropper: {
				showGrid: false,
				ratio: '1:1',
				minWidth: 200,
				minHeight: 200,
			},
			onSave: function (base64, item, listEl, parentEl, newInputEl, inputEl) {
				var api = $.fileuploader.getInstance(inputEl);

				// blob
				item.editor._blob = api.assets.dataURItoBlob(base64, item.type);

				if (item.upload) {
					if (api.getFiles().length == 2 && (api.getFiles()[0].data.isDefault || api.getFiles()[0].upload))
						api.getFiles()[0].remove();
					parentEl.find('.fileuploader-menu ul a').show();

					if (item.upload.send)
						return item.upload.send();
					if (item.upload.resend)
						return item.upload.resend();
				} else if (item.appended) {
					var form = new FormData();

					// hide current thumbnail (this is only animation)
					item.image.addClass('fileuploader-loading').html('');
					item.html.find('.fileuploader-action-popup').hide();
					parentEl.find('[data-action="fileuploader-edit"]').hide();

					// send ajax
					form.append(inputEl.attr('name'), item.editor._blob);
					form.append('fileuploader', true);
					form.append('name', item.name);
					form.append('editing', true);
					$.ajax({
						url: api.getOptions().upload.url,
						data: form,
						type: 'POST',
						processData: false,
						contentType: false
					}).always(function () {
						delete item.isSaving;
						item.reader.read(function () {
							item.html.find('.fileuploader-action-popup').show();
							parentEl.find('[data-action="fileuploader-edit"]').show();
							item.popup.html = item.popup.editor = item.editor.crop = item.editor.rotation = item.popup.zoomer = null;
							item.renderThumbnail();
						}, null, true);
					});
				}
			}
		},
		upload: {
			url: "upload/avatar",
			data: null, // should be null
			type: 'POST',
			enctype: 'multipart/form-data',
			start: false,
			beforeSend: function (item, listEl, parentEl, newInputEl, inputEl) {
				item.upload.formData = new FormData();

				if (item.editor && item.editor._blob) {
					item.upload.data.fileuploader = 1;
					item.upload.data.name = item.name;
					item.upload.data.editing = item.uploaded;

					item.upload.formData.append(inputEl.attr('name'), item.editor._blob, item.name);
				}

				item.image.hide();
				item.html.removeClass('upload-complete');
				parentEl.find('[data-action="fileuploader-edit"]').hide();
				this.onProgress({ percentage: 0 }, item);
			},
			onSuccess: function (result, item, listEl, parentEl, newInputEl, inputEl) {
				var api = $.fileuploader.getInstance(inputEl),
					$progressBar = item.html.find('.progressbar3'),
					data = {};

				if (result && result.files)
					data = result;
				else
					data.hasWarnings = true;

				if (api.getFiles().length > 1)
					api.getFiles()[0].remove();

				// if success
				if (data.isSuccess && data.files[0]) {
					item.name = data.files[0].name;
				}

				// if warnings
				if (data.hasWarnings) {
					for (var warning in data.warnings) {
						alert(data.warnings[warning]);
					}

					item.html.removeClass('upload-successful').addClass('upload-failed');
					return this.onError ? this.onError(item) : null;
				}

				delete item.isSaving;
				item.html.addClass('upload-complete').removeClass('is-image-waiting');
				$progressBar.find('span').html('<i class="fileuploader-icon-success"></i>');
				parentEl.find('[data-action="fileuploader-edit"]').show();
				setTimeout(function () {
					$progressBar.fadeOut(450);
				}, 1250);
				item.image.fadeIn(250);
			},
			onError: function (item, listEl, parentEl, newInputEl, inputEl) {
				var $progressBar = item.html.find('.progressbar3');

				item.html.addClass('upload-complete');
				if (item.upload.status != 'cancelled')
					$progressBar.find('span').attr('data-action', 'fileuploader-retry').html('<i class="fileuploader-icon-retry"></i>');
			},
			onProgress: function (data, item) {
				var $progressBar = item.html.find('.progressbar3');

				if (data.percentage == 0)
					$progressBar.addClass('is-reset').fadeIn(250).html('');
				else if (data.percentage >= 99)
					data.percentage = 100;
				else
					$progressBar.removeClass('is-reset');
				if (!$progressBar.children().length)
					$progressBar.html('<span></span><svg><circle class="progress-dash"></circle><circle class="progress-circle"></circle></svg>');

				var $span = $progressBar.find('span'),
					$svg = $progressBar.find('svg'),
					$bar = $svg.find('.progress-circle'),
					hh = Math.max(60, item.html.height() / 2),
					radius = Math.round(hh / 2.28),
					circumference = radius * 2 * Math.PI,
					offset = circumference - data.percentage / 100 * circumference;

				$svg.find('circle').attr({
					r: radius,
					cx: hh,
					cy: hh
				});
				$bar.css({
					strokeDasharray: circumference + ' ' + circumference,
					strokeDashoffset: offset
				});

				$span.html(data.percentage + '%');
			},
			onComplete: null,
		},
		afterRender: function (listEl, parentEl, newInputEl, inputEl) {
			var api = $.fileuploader.getInstance(inputEl);

			// remove multiple attribute
			inputEl.removeAttr('multiple');

			// set drop container
			api.getOptions().dragDrop.container = parentEl.find('.fileuploader-wrapper');

			// disabled input
			if (api.isDisabled()) {
				parentEl.find('.fileuploader-menu').remove();
			}

			// [data-action]
			parentEl.on('click', '[data-action]', function () {
				var $this = $(this),
					action = $this.attr('data-action'),
					item = api.getFiles().length ? api.getFiles()[api.getFiles().length - 1] : null;

				switch (action) {
					case 'fileuploader-input':
						api.open();
						break;
					case 'fileuploader-edit':
						if (item && item.popup) {
							item.popup.open();
							item.editor.cropper();
						}
						break;
					case 'fileuploader-retry':
						if (item && item.upload.retry)
							item.upload.retry();
						break;
					case 'fileuploader-remove':
						if (item)
							item.remove();
						break;
				}
			});

			// menu
			$('body').on('click', function (e) {
				var $target = $(e.target),
					$parent = $target.closest('.fileuploader');

				$('.fileuploader-menu').removeClass('is-shown');
				if ($target.is('.fileuploader-menu-open') || $target.closest('.fileuploader-menu-open').length)
					$parent.find('.fileuploader-menu').addClass('is-shown');
			});
		},
		onEmpty: function (listEl, parentEl, newInputEl, inputEl) {
			var api = $.fileuploader.getInstance(inputEl),
				defaultAvatar = inputEl.attr('data-fileuploader-default');

			if (defaultAvatar && !listEl.find('> .is-default').length)
				api.append({ name: '', type: 'image/png', size: 0, file: defaultAvatar, data: { isDefault: true, popup: false, listProps: { is_default: true } } });

			parentEl.find('.fileuploader-menu ul a').hide().filter('[data-action="fileuploader-input"]').show();
		},

		onRemove: function (item) {
			if (item.name && (item.appended || item.uploaded))
				$.post('remove/avatar', {
					file: item.name
				});
		},

		captions: $.extend(true, {}, $.fn.fileuploader.languages['fr'], {
			edit: 'Edit',
			upload: 'Upload',
			errors: {
				filesLimit: 'Only 1 file is allowed to be uploaded.',
			}
		})
	});
});


$(document).ready(function () {

	// editor save function
	var saveEditedImage = function (item) {
		// if still uploading
		// pend and exit
		if (item.upload && item.upload.status == 'loading')
			return item.editor.isUploadPending = true;

		// if not preloaded or not uploaded
		if (!item.appended && !item.uploaded)
			return;

		// if no editor
		if (!item.editor || !item.reader.width)
			return;

		// if uploaded
		// resend upload
		if (item.upload && item.upload.resend) {
			item.upload.resend();
		}

		// if preloaded
		// send request
		if (item.appended) {
			// hide current thumbnail (this is only animation)
			item.imageIsUploading = true;
			item.image.addClass('fileuploader-loading').html('');
			item.html.find('.fileuploader-action-popup').hide();

			$.post('php/ajax_resize_file.php', { _file: item.file, _editor: JSON.stringify(item.editor), fileuploader: 1 }, function () {
				item.reader.read(function () {
					delete item.imageIsUploading;
					item.html.find('.fileuploader-action-popup').show();

					item.popup.html = item.popup.editor = item.editor.crop = item.editor.rotation = null;
					item.renderThumbnail();
				}, null, true);
			});
		}
	};

	// enable fileuploader plugin
	$('#images_form_imageFile').fileuploader({
		extensions: ['JPG', 'jpg', 'jpeg', 'JPEG'],
		changeInput: '<div class="fileuploader-input">' +
			'<div class="fileuploader-input-inner">' +
			'<div class="fileuploader-icon-main d-block font-20 mb-2"></div>' +
			'<h3 class="fileuploader-input-caption"><span>${captions.feedback}</span></h3>' +
			'<p>${captions.or}</p>' +
			'<button type="button" class="fileuploader-input-button"><span>${captions.button}</span></button>' +
			'</div>' +
			'</div>',
		theme: 'dragdrop',
		limit: 14,
		thumbnails: {
			onImageLoaded: function (item) {
				if (!item.html.find('.fileuploader-action-edit').length)
					item.html.find('.fileuploader-action-remove').before('<button type="button" class="fileuploader-action fileuploader-action-popup fileuploader-action-edit" title="Edit"><i class="fileuploader-icon-edit"></i></button>');

				if (item.imageIsUploading) {
					item.image.addClass('fileuploader-loading').html('');
				}
			}
		},
		onRemove: function (item) {
		},
		editor: {
			cropper: {
				showGrid: true,
			},
			onSave: function (dataURL, item) {
				saveEditedImage(item);
			}
		},

		captions: $.extend(true, {}, $.fn.fileuploader.languages['fr', 'en'], {
			feedback: 'Faites glisser vos photos ici',
			feedback2: 'Faites glisser vos photos ici',
			drop: 'Faites glisser vos photos ici',
			or: 'ou',
			button: 'Sélectionner vos photos sur votre ordinateur',
			name: 'Nom',
			Type: 'Format',
			size: 'Taille du fichier',
			dimensions: 'Dimensions',
			crop: 'Cropper',
			rotate: 'Rotation',
			remove: 'Supprimer',
			cancel: 'Annuler',
			confirm: 'Sauvegarder',
			removeConfirmation: 'Êtes-vous sûre de vouloir supprimer cette photo ?',
			errors: {
				filesLimit: "Seuls ${limit} fichiers peuvent être téléchargés en même temps.",
				filesType: "Merci de choisir un fichier avec une extension .jpg ou jpeg.",
				fileSize: "${name} est trop volumineux. Merci de choisir une image inférieure ou égale à ${fileMaxSize} MB.",
				filesSizeAll: "Les fichiers que vous avez choisis sont trop volumineux Téléchargez des fichiers jusqu'à ${maxSize} MB.",
				fileName: 'File with the name ${name} is already selected.',
				folderUpload: 'You are not allowed to upload folders.'
			}
		}),
	});

});

$(document).ready(function () {

	// enable fileuploader plugin
	var container = document.getElementById('gallery');
	var folderId = container.dataset.folder;

	var $fileuploader = $('input.gallery_media').fileuploader({
		limit: 100,
		fileMaxSize: 8,
		extensions: ['jpg', 'JPG', 'jpeg', 'JPEG'],
		changeInput: ' ',
		theme: 'gallery',
		enableApi: true,
		thumbnails: {
			box: '<div class="fileuploader-items">' +
				'<ul class="fileuploader-items-list">' +
				'<li class="fileuploader-input"><button type="button" class="fileuploader-input-inner"><i class="fileuploader-icon-main"></i> <span>${captions.feedback}</span></button></li>' +
				'</ul>' +
				'</div>',
			item: '<li class="fileuploader-item">' +
				'<div class="fileuploader-item-inner card mb-0 border pt-4">' +
				'<div class="actions-holder">' +
				'<button type="button" class="fileuploader-action fileuploader-action-sort is-hidden" title="${captions.sort}"><i class="fe-move handle"></i></button>' +
				'<button type="button" class="fileuploader-action fileuploader-action-settings is-hidden" title="${captions.edit}"><i class="fileuploader-icon-settings"></i></button>' +
				'<button type="button" class="fileuploader-action fileuploader-action-remove" title="${captions.remove}"><i class="fileuploader-icon-remove"></i></button>' +
				'<div class="dropdown-menu dropdown-menu-right gallery-item-dropdown">' +
				'<a class="dropdown-item" href="${data.url}">${captions.setting_open}</a>' +
				'<a class="dropdown-item gallery-action-asmain">${captions.setting_asMain}</a>' +
				'</div>' +
				'</div>' +
				'<div class="thumbnail-holder">' +
				'<a href="${data.url}">${image}</a>' +
				'<div class="progress-holder"><span></span>${progressBar}</div>' +
				'</div>' +
				'<div class="content-holder pb-0"><h5 title="${name}">${name}</h5><span>${size2}</span></div>' +
				'</div>' +
				'</li>',
			item2: '<li class="fileuploader-item file-main-${data.isMain}">' +
				'<div class="fileuploader-item-inner card mb-0 border pt-4">' +
				'<div class="actions-holder">' +
				'<button type="button" class="fileuploader-action fileuploader-action-sort" title="${captions.sort}"><i class="fe-move handle"></i></button>' +
				'<button type="button" class="fileuploader-action fileuploader-action-settings" title="${captions.edit}"><i class="fileuploader-icon-settings"></i></button>' +
				'<button type="button" class="fileuploader-action fileuploader-action-remove" title="${captions.remove}"><i class="fileuploader-icon-remove"></i></button>' +
				'<div class="dropdown-menu dropdown-menu-right text-right gallery-item-dropdown">' +
				'<a class="dropdown-item" href="${data.url}">${captions.setting_open}</a>' +
				'<a class="dropdown-item gallery-action-asmain">${captions.setting_asMain}</a>' +
				'</div>' +
				'</div>' +
				'<div class="thumbnail-holder">' +
				'<a href="${data.url}">${image}</a>' +
				'</div>' +
				'<div class="content-holder pb-0"><h5 title="${name}">${name}</h5><span>${size2}</span></div>' +
				'</div>' +
				'</li>',
			itemPrepend: true,
			startImageRenderer: true,
			canvasImage: false,
			onItemShow: function (item, listEl, parentEl, newInputEl, inputEl) {
				var api = $.fileuploader.getInstance(inputEl),
					color = api.assets.textToColor(item.format),
					$plusInput = listEl.find('.fileuploader-input'),
					$progressBar = item.html.find('.progress-holder');

				// put input first in the list
				$plusInput.prependTo(listEl);

				// color the icon and the progressbar with the format color
				//item.html.find('.type-holder .fileuploader-item-icon')[api.assets.isBrightColor(color) ? 'addClass' : 'removeClass']('is-bright-color').css('backgroundColor', color);
				$progressBar.css('backgroundColor', color);
			},
			onImageLoaded: function (item, listEl, parentEl, newInputEl, inputEl) {
				var api = $.fileuploader.getInstance(inputEl);

				// add icon
				//item.image.find('.fileuploader-item-icon i').html('')
				//.addClass('fileuploader-icon-' + (['image', 'video', 'audio'].indexOf(item.format) > -1 ? item.format : 'file'));

				// check the image size
				if (item.format == 'image' && item.upload && !item.imU) {
					if (item.reader.node && (item.reader.width < 100 || item.reader.height < 100)) {
						alert(api.assets.textParse(api.getOptions().captions.imageSizeError, item));
						return item.remove();
					}

					item.image.hide();
					item.reader.done = true;
					item.upload.send();
				}

			},
			onItemRemove: function (html) {
				html.fadeOut(250);
			}
		},
		dragDrop: {
			container: '.fileuploader-theme-gallery .fileuploader-input'
		},
		upload: {
			url: Routing.generate('add_image', { id: folderId }),
			data: null,
			type: 'POST',
			enctype: 'multipart/form-data',
			start: true,
			synchron: true,
			beforeSend: function (item) {
				// check the image size first (onImageLoaded)
				if (item.format == 'image' && !item.reader.done)
					return false;

				// add editor to upload data after editing
				if (item.editor && (typeof item.editor.rotation != "undefined" || item.editor.crop)) {
					item.imU = true;
					item.upload.data.name = item.name;
					item.upload.data.id = item.data.listProps.id;
					item.upload.data._editorr = JSON.stringify(item.editor);
				}

				item.html.find('.fileuploader-action-success').removeClass('fileuploader-action-success');
			},
			onSuccess: function (result, item) {
				var data = {};

				try {
					data = JSON.parse(result);
				} catch (e) {
					data.hasWarnings = true;
				}

				// if success update the information
				if (data.isSuccess && data.files.length) {
					if (!item.data.listProps)
						item.data.listProps = {};
					item.title = data.files[0].title;
					item.name = data.files[0].name;
					item.size = data.files[0].size;
					item.size2 = data.files[0].size2;
					item.data.url = data.files[0].url;
					item.data.listProps.id = data.files[0].id;
					item.date = data.files[0].date;

					item.html.find('.content-holder h5').attr('title', item.name).text(item.name);
					item.html.find('.content-holder span').text(item.size2);
					item.html.find('.content-holder span.date').text(item.date);
					item.html.find('.gallery-item-dropdown [download]').attr('href', item.data.url);
				}

				// if warnings
				if (data.hasWarnings) {
					for (var warning in data.warnings) {
						alert(data.warnings[warning]);
					}

					item.html.removeClass('upload-successful').addClass('upload-failed');
					return this.onError ? this.onError(item) : null;
				}

				delete item.imU;
				item.html.find('.fileuploader-action-remove').addClass('fileuploader-action-success');

				setTimeout(function () {
					item.html.find('.progress-holder').hide();

					item.html.find('.fileuploader-action-popup, .fileuploader-item-image').show();
					item.html.find('.fileuploader-action-sort').removeClass('is-hidden');
					item.html.find('.fileuploader-action-settings').removeClass('is-hidden');
				}, 400);
			},
			onError: function (item) {
				item.html.find('.progress-holder, .fileuploader-action-popup, .fileuploader-item-image').hide();

				// add retry button
				item.upload.status != 'cancelled' && !item.imU && !item.html.find('.fileuploader-action-retry').length ? item.html.find('.actions-holder').prepend(
					'<button type="button" class="fileuploader-action fileuploader-action-retry" title="Retry"><i class="fileuploader-icon-retry"></i></button>'
				) : null;
			},
			onProgress: function (data, item) {
				var $progressBar = item.html.find('.progress-holder');

				if ($progressBar.length) {
					$progressBar.show();
					$progressBar.find('span').text(data.percentage >= 99 ? 'Uploading...' : data.percentage + '%');
					$progressBar.find('.fileuploader-progressbar .bar').height(data.percentage + '%');
				}

				item.html.find('.fileuploader-action-popup, .fileuploader-item-image').hide();
			}
		},
		editor: {
			cropper: {
				showGrid: true,
				minWidth: 100,
				minHeight: 100
			},
			onSave: function (dataURL, item) {
				// if no editor
				if (!item.editor || !item.reader.width)
					return;

				// if uploaded
				// resend upload
				if (item.upload && item.upload.resend)
					item.upload.resend();

				// if preloaded
				// send request
				if (item.appended && item.data.listProps) {
					// hide current thumbnail
					item.imU = true;
					item.image.addClass('fileuploader-loading').find('img, canvas').hide();
					item.html.find('.fileuploader-action-popup').hide();

					$.post('php/ajax.php?type=resize', { name: item.name, id: item.data.listProps.id, _editor: JSON.stringify(item.editor) }, function () {
						// update the image
						item.reader.read(function () {
							delete item.imU;

							item.image.removeClass('fileuploader-loading').find('img, canvas').show();
							item.html.find('.fileuploader-action-popup').show();
							item.editor.rotation = item.editor.crop = null;
							item.popup = { open: item.popup.open };
						}, null, true);
					});
				}
			}
		},
		sorter: {
			onSort: function (list, listEl, parentEl, newInputEl, inputEl) {
				var api = $.fileuploader.getInstance(inputEl),
					fileList = api.getFiles(),
					list = [];

				// prepare the sorted list
				api.getFiles().forEach(function (item) {
					if (item.data.listProps)
						list.push({
							name: item.name,
							id: item.data.listProps.id,
							index: item.index
						});
				});

				// send request
				$.post(Routing.generate('sort_images', { id: folderId }), {
					list: JSON.stringify(list)
				});
			}
		},
		afterRender: function (listEl, parentEl, newInputEl, inputEl) {
			var api = $.fileuploader.getInstance(inputEl),
				$plusInput = listEl.find('.fileuploader-input');

			// bind input click
			$plusInput.on('click', function () {
				api.open();
			});

			// set drop container
			api.getOptions().dragDrop.container = $plusInput;

			// bind dropdown buttons
			$('body').on('click', function (e) {
				var $target = $(e.target),
					$item = $target.closest('.fileuploader-item'),
					item = api.findFile($item);

				// toggle dropdown
				$('.gallery-item-dropdown').hide();
				if ($target.is('.fileuploader-action-settings') || $target.parent().is('.fileuploader-action-settings')) {
					$item.find('.gallery-item-dropdown').show(150);
				}

				// rename
				if ($target.is('.gallery-action-rename')) {
					var x = prompt(api.getOptions().captions.rename, item.title);

					if (x && item.data.listProps) {
						$.post(Routing.generate('rename_images', { folderId: folderId, name: item.name, id: item.data.listProps.id, title: x }), function (result) {
							try {
								var j = JSON.parse(result);

								// update the file name and url
								if (j.title) {
									item.title = j.title;
									item.name = item.title + (item.extension.length ? '.' + item.extension : '');
									$item.find('.content-holder h5').attr('title', item.name).html(item.name);
									$item.find('.gallery-item-dropdown [download]').attr('href', item.data.url);

									if (item.popup.html)
										item.popup.html.find('h5:eq(0)').text(item.name);

									if (j.url)
										item.data.url = j.url;
									if (item.appended && j.file)
										item.file = j.file;

									api.updateFileList();
								}

							} catch (e) {
								alert(api.getOptions().captions.renameError);
							}
						});
					}
				}

				// set main
				if ($target.is('.gallery-action-asmain') && item.data.listProps) {
					$.post(Routing.generate('main_image', { galleryId: folderId, name: item.name, id: item.data.listProps.id }), function () {
						api.getFiles().forEach(function (val) {
							delete val.data.isMain;
							val.html.removeClass('file-main-0 file-main-1');
						});
						item.html.addClass('file-main-1');
						item.data.isMain = true;

						api.updateFileList();
					});
				}
			});
		},
		onRemove: function (item) {
			// send request
			if (item.data.listProps)
				$.post(Routing.generate('remove_image', { id: folderId, mediaId: item.data.listProps.id }), {
					name: item.name,
					id: item.data.listProps.id
				});
		},
		captions: $.extend(true, {}, $.fn.fileuploader.languages['fr'], {
			feedback: 'Ajouter des photos ici.<br> 8Mo par fichier maximum',
			setting_asMain: 'Mettre en couverture',
			setting_edit: 'Modifier',
			setting_open: 'Ouvrir',
			setting_rename: 'Modifier le titre',
			rename: 'Saisissez le titre de l\'image:',
			renameError: 'Please enter another name.',
			imageSizeError: 'The image ${name} is too small.',
		})
	});

	// preload the files

	$.post(Routing.generate('preload_images', { id: folderId }), null, function (result) {
		var api = $.fileuploader.getInstance($fileuploader),
			preload = [];

		try {
			// preload the files
			preload = JSON.parse(result);

			api.append(preload);
		} catch (e) { }
	});
});


$(document).ready(function () {
	$('.editable').editable({
		mode: 'popup',
	});
});
